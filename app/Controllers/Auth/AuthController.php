<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\AuthModel;

class AuthController extends BaseController
{
    protected $authModel;
    protected $session;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->session = \Config\Services::session();
        helper(['form', 'url']);
    }

    // Halaman login
    public function login()
    {
        // Jika sudah login, redirect ke dashboard
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/menu');
        }

        return view('Auth/login');
    }

    public function processLogin()
{
    // Validasi input
    $rules = [
        'username' => 'required',
        'password' => 'required'
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    $username = $this->request->getPost('username');
    $password = $this->request->getPost('password');
    
    // DEBUG: Tampilkan input
    log_message('debug', "=== LOGIN ATTEMPT ===");
    log_message('debug', "Username: $username");
    log_message('debug', "Password: $password");

    // Cari user di database
    $db = \Config\Database::connect('db_auth');
    $builder = $db->table('users');
    
    // Query user
    $user = $builder->where('username', $username)
                    ->where('is_active', 1)
                    ->get()
                    ->getRowArray();
    
    log_message('debug', "User query result: " . print_r($user, true));
    
    // Cek apakah user ditemukan
    if (!$user) {
        log_message('debug', "User NOT found in database");
        return redirect()->back()->withInput()->with('error', 'Username tidak ditemukan atau akun tidak aktif.');
    }
    
    // Verifikasi password (plain text untuk sekarang)
    if ($password !== $user['password']) {
        log_message('debug', "Password mismatch. Input: $password, DB: {$user['password']}");
        return redirect()->back()->withInput()->with('error', 'Password salah.');
    }
    
    log_message('debug', "Password verified successfully");

    // Set session data dengan cara yang benar
    $sessionData = [
        'userId' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'fullName' => $user['full_name'],
        'role' => $user['role'],
        'isLoggedIn' => TRUE
    ];
    
    // Set session
    $this->session->set($sessionData);
    
    // Verify session is set
    log_message('debug', "Session after set: " . print_r($this->session->get(), true));

    // Update last login
    $builder->where('id', $user['id'])
            ->update(['last_login' => date('Y-m-d H:i:s')]);

    // Redirect ke menu
    log_message('debug', "Redirecting to menu page");
    return redirect()->to('/menu');
}

    // Redirect berdasarkan role
    private function redirectBasedOnRole($role)
    {
        switch ($role) {
            case 'admin':
                return redirect()->to('/menu');
            case 'operator':
                return redirect()->to('/menu');
            default:
                return redirect()->to('/menu');
        }
    }

    // Set remember me cookie
    private function setRememberMe($userId)
    {
        $token = bin2hex(random_bytes(32));
        $expire = time() + (30 * 24 * 60 * 60); // 30 hari
        
        // Simpan token di database (buat tabel remember_tokens jika perlu)
        // Untuk sementara, simpan di cookie
        $cookie = [
            'name'   => 'remember_token',
            'value'  => $token,
            'expire' => $expire,
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ];
        
        set_cookie($cookie);
    }

    // Logout
    public function logout()
{
    $session = session();
    
    log_message('debug', '=== LOGOUT PROCESS ===');
    
    // Cek apakah user sedang login
    if ($session->get('isLoggedIn')) {
        log_message('debug', 'Logging out user: ' . $session->get('username'));
    }
    
    // Hapus semua data session TANPA regenerate setelah destroy
    $session->destroy();
    
    // Clear session cookie juga
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    log_message('debug', 'Session destroyed successfully');
    
    // Redirect ke login page dengan pesan sukses
    return redirect()->to('/auth/login')->with('success', 'Anda telah logout.');
}

    // Halaman lupa password
    public function forgotPassword()
    {
        return view('Auth/forgot_password');
    }

    // Halaman reset password
    public function resetPassword($token = null)
    {
        if (!$token) {
            return redirect()->to('/auth/forgot-password');
        }

        $data['token'] = $token;
        return view('Auth/reset_password', $data);
    }

    // Tambahkan di AuthController.php
public function debugLogin()
{
    return view('Auth/debug_login');
}

public function checkSession()
{
    echo '<pre>';
    echo 'Session Data:<br>';
    print_r($this->session->get());
    echo '</pre>';
    
    echo '<a href="/auth/login">Back to Login</a>';
}

public function resetAdminPassword()
{
    $password = $this->request->getPost('password');
    if (!$password) {
        return redirect()->to('/debug/login')->with('error', 'Password required');
    }
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $db = \Config\Database::connect('db_auth');
    $builder = $db->table('users');
    
    $result = $builder->where('username', 'admin')
                     ->update(['password' => $hashedPassword]);
    
    if ($result) {
        return redirect()->to('/debug/login')->with('success', 'Admin password reset successfully');
    } else {
        return redirect()->to('/debug/login')->with('error', 'Failed to reset password');
    }
}
public function checkLogin()
{
    $session = session();
    $isLoggedIn = $session->get('isLoggedIn');
    
    if (!$isLoggedIn) {
        log_message('debug', "User is NOT logged in - redirecting to login");
        return redirect()->to('/auth/login');
    }
    
    log_message('debug', "User IS logged in as: " . $session->get('username'));
    return true;
}
}