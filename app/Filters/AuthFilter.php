<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\RedirectResponse;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Debug log
        log_message('debug', 'AuthFilter: Checking if user is logged in');
        log_message('debug', 'AuthFilter: Current URL - ' . current_url());
        log_message('debug', 'AuthFilter: Session data - ' . print_r($session->get(), true));
        
        // Cek apakah user sudah login
        if (!$session->get('isLoggedIn')) {
            log_message('debug', 'AuthFilter: User NOT logged in - Redirecting to login');
            
            // Simpan URL yang diakses untuk redirect setelah login
            $session->set('redirect_url', current_url());
            
            // Return redirect response
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu!');
        }
        
        log_message('debug', 'AuthFilter: User IS logged in - Access granted for: ' . $session->get('username'));
        
        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here if needed
        return $response;
    }
}