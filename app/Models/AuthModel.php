<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{
    // Spesifikasikan database connection
    protected $DBGroup = 'db_auth';
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password', 'email', 'full_name', 'role', 'is_active', 'last_login'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Cari user berdasarkan username
    public function getUserByUsername($username)
    {
        return $this->where('username', $username)
                    ->where('is_active', 1)
                    ->first();
    }

    // Update last login
    public function updateLastLogin($userId)
    {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }

    // Record login attempt
    public function recordLoginAttempt($ip, $username, $success)
    {
        // Gunakan builder yang sama dengan DBGroup db_auth
        $builder = $this->db->table('login_attempts');
        return $builder->insert([
            'ip_address' => $ip,
            'username' => $username,
            'success' => $success
        ]);
    }

    // Cek login attempts (brute force protection)
    public function checkLoginAttempts($ip, $username, $minutes = 15)
    {
        $builder = $this->db->table('login_attempts');
        
        $time = date('Y-m-d H:i:s', strtotime("-$minutes minutes"));
        
        return $builder->where('ip_address', $ip)
            ->where('username', $username)
            ->where('attempt_time >=', $time)
            ->where('success', 0)
            ->countAllResults();
    }
}