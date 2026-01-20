<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Halaman Login
     */
    public function index()
    {
        // Jika sudah login, redirect ke dashboard
        if ($this->session->get('logged_in')) {
            return redirect()->to('/user/dashboard');
        }

        $data = [
            'title' => 'Login - EventKu',
            'validation' => \Config\Services::validation()
        ];

        return view('auth/login', $data);
    }

    /**
     * Halaman Register
     */
    public function register()
    {
        // Jika sudah login, redirect ke dashboard
        if ($this->session->get('logged_in')) {
            return redirect()->to('/user/dashboard');
        }

        $data = [
            'title' => 'Register - EventKu',
            'validation' => \Config\Services::validation()
        ];

        return view('auth/register', $data);
    }

    /**
     * Proses Login (POST)
     */
    public function doLogin()
    {
        // Validasi input
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Cek user di database
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Email tidak terdaftar');
        }

        // Verifikasi password
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Password salah');
        }

        // Set session
        $sessionData = [
            'user_id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'logged_in' => true
        ];

        $this->session->set($sessionData);

        return redirect()->to('/user/dashboard')->with('success', 'Login berhasil! Selamat datang ' . $user['name']);
    }

    /**
     * Proses Register (POST)
     */
    public function doRegister()
    {
        // Validasi input ATAU  Proses login dengan hash password
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'phone' => 'required|min_length[10]|max_length[20]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]'
        ];

        $messages = [
            'email' => [
                'is_unique' => 'Email sudah terdaftar, silakan gunakan email lain'
            ],
            'password_confirm' => [
                'matches' => 'Konfirmasi password tidak cocok'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Simpan data user
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'password' => $this->request->getPost('password') // Will be hashed by model
        ];

        try {
            $this->userModel->insert($data);

            return redirect()->to('/auth/login')->with('success', 'Registrasi berhasil! Silakan login');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Registrasi gagal: ' . $e->getMessage());
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/auth/login')->with('success', 'Logout berhasil');
    }

    /**
     * Halaman Forgot Password
     */
    public function forgotPassword()
    {
        $data = [
            'title' => 'Lupa Password - EventKu',
            'validation' => \Config\Services::validation()
        ];

        return view('auth/forgot_password', $data);
    }

    /**
     * Proses Reset Password (POST)
     * Dipanggil setelah OTP terverifikasi
     */
    public function doResetPassword()
    {
        // Validasi input
        $rules = [
            'phone' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Password tidak valid atau tidak sama');
        }

        $phone = $this->request->getPost('phone');
        $newPassword = $this->request->getPost('new_password');

        // Cari user berdasarkan nomor HP
        $user = $this->userModel->where('phone', $phone)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Nomor telepon tidak terdaftar');
        }

        // Update password
        $updateData = [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ];

        if ($this->userModel->update($user['id'], $updateData)) {
            return redirect()->to('/auth/login')->with('success', 'Password berhasil direset! Silakan login dengan password baru.');
        } else {
            return redirect()->back()->with('error', 'Gagal mereset password. Silakan coba lagi.');
        }
    }
}
