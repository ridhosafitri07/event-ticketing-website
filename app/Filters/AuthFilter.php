<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Check if user is logged in
     * Jika belum login, redirect ke halaman login
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Cek apakah user sudah login
        if (!$session->get('logged_in')) {
            // Simpan URL yang diminta untuk redirect setelah login
            $session->set('redirect_url', current_url());
            
            // Redirect ke login page
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu');
        }
    }

    /**
     * After filter (tidak digunakan)
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
