<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuthFilter implements FilterInterface
{
    /**
     * Check if admin is logged in
     * Jika belum login sebagai admin, redirect ke admin login
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Cek apakah admin sudah login
        if (!$session->get('admin_logged_in')) {
            // Redirect ke admin login page
            return redirect()->to('/admin/login')->with('error', 'Silakan login sebagai admin terlebih dahulu');
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
