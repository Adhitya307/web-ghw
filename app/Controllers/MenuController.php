<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class MenuController extends BaseController
{
    public function index()
    {
        $session = session();
        
        // Cek apakah user sudah login
        if (!$session->get('isLoggedIn')) {
            // HAPUS SEMUA DEBUG OUTPUT
            return redirect()->to('/auth/login');
        }
        
        // Data untuk view
        $data = [
            'title' => 'Menu Utama - Monitoring PLTA Saguling',
            'username' => $session->get('username'),
            'fullName' => $session->get('fullName'),
            'role' => $session->get('role'),
            'email' => $session->get('email'),
            'isAdmin' => ($session->get('role') == 'admin') // Flag untuk UI
        ];
        
        // Return view tanpa debug
        return view('menu', $data);
    }
}