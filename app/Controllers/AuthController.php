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
            log_message('error', 'Login failed - Email not found: ' . $email);
            return redirect()->back()->withInput()->with('error', 'Email tidak terdaftar');
        }
        
        // Debug log
        log_message('info', 'Login attempt - Email: ' . $email . ' | User ID: ' . $user['id']);

        // Verifikasi password
        if (!password_verify($password, $user['password'])) {
            log_message('error', 'Login failed - Wrong password for email: ' . $email);
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
     * API: Kirim OTP via WhatsApp (Python)
     */
    public function sendOTP()
    {
        // Set JSON response header
        $this->response->setContentType('application/json');

        $input = $this->request->getJSON();
        
        // Fallback jika bukan JSON, coba dari POST
        if (!$input) {
            $input = (object) $this->request->getPost();
        }

        $name = $input->name ?? '';
        $phone = $input->phone ?? '';

        if (!$name || !$phone) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nama dan nomor HP harus diisi']);
        }

        // Format nomor HP ke format internasional (62xxx)
        $formattedPhone = $this->formatPhoneNumber($phone);
        
        // Format alternatif (08xxx)
        $altPhone = $phone;
        if (substr($formattedPhone, 0, 2) === '62') {
            $altPhone = '0' . substr($formattedPhone, 2);
        }

        // Cek apakah nomor HP terdaftar (cek berbagai format)
        $user = $this->userModel->groupStart()
                                    ->where('phone', $phone)
                                    ->orWhere('phone', $formattedPhone)
                                    ->orWhere('phone', $altPhone)
                                ->groupEnd()
                                ->first();

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'Nomor HP tidak terdaftar. Silakan register terlebih dahulu.'
            ]);
        }

        // Hit Python API untuk kirim OTP
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:5000/send-otp');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'phone' => $formattedPhone,
                'name' => $name
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30 detik cukup untuk kirim WhatsApp
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Jika curl error, return error ke user
            if ($curlError) {
                log_message('error', 'CURL Error sending OTP: ' . $curlError);
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal mengirim OTP. Pastikan server WhatsApp berjalan!'
                ]);
            }

            if ($httpCode === 200 && $response) {
                $result = json_decode($response, true);
                
                if (isset($result['status']) && $result['status'] === 'success') {
                    // Simpan OTP di session untuk verifikasi - PASTIKAN STRING
                    $otpCode = (string) $result['otp'];
                    $this->session->set('otp_code', $otpCode);
                    $this->session->set('otp_phone', $formattedPhone);
                    $this->session->set('otp_time', time());
                    
                    // Debug log
                    log_message('info', 'OTP Generated: ' . $otpCode . ' for phone: ' . $formattedPhone);

                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'OTP berhasil dikirim ke WhatsApp',
                        'phone' => $formattedPhone,
                        'debug_otp' => $otpCode  // Temporary debug
                    ]);
                } else {
                    // Python return error
                    log_message('error', 'Python API error: ' . json_encode($result));
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => $result['message'] ?? 'Gagal mengirim OTP'
                    ]);
                }
            }

            // Jika response tidak valid
            log_message('error', 'Invalid response from Python API. HTTP Code: ' . $httpCode);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengirim OTP. Server error.'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Exception sending OTP: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * API: Verifikasi OTP
     */
    public function verifyOTP()
    {
        // Set JSON response header
        $this->response->setContentType('application/json');

        $input = $this->request->getJSON();
        
        // Fallback jika bukan JSON
        if (!$input) {
            $input = (object) $this->request->getPost();
        }

        $otp = $input->otp ?? '';
        $phone = $input->phone ?? '';

        // Cek session OTP
        $sessionOTP = $this->session->get('otp_code');
        $sessionPhone = $this->session->get('otp_phone');
        $sessionTime = $this->session->get('otp_time');
        
        // Debug log
        log_message('info', 'Verify OTP - Input: ' . $otp . ' (type: ' . gettype($otp) . ') vs Session: ' . $sessionOTP . ' (type: ' . gettype($sessionOTP) . ')');
        log_message('info', 'Verify Phone - Input: ' . $phone . ' vs Session: ' . $sessionPhone);

        // Validasi
        if (!$sessionOTP) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'OTP tidak ditemukan. Silakan kirim ulang.']);
        }

        // Cek expired (5 menit)
        if (time() - $sessionTime > 300) {
            $this->session->remove('otp_code');
            return $this->response->setJSON(['status' => 'error', 'message' => 'OTP sudah kadaluarsa. Silakan kirim ulang.']);
        }

        // Cek OTP cocok (konversi ke string dan trim untuk memastikan)
        $inputOtp = trim((string) $otp);
        $sessionOtpStr = trim((string) $sessionOTP);
        $inputPhone = trim((string) $phone);
        $sessionPhoneStr = trim((string) $sessionPhone);
        
        if ($inputOtp != $sessionOtpStr || $inputPhone != $sessionPhoneStr) {
            // Debug: log untuk troubleshooting
            log_message('error', 'OTP Mismatch - Input: "' . $inputOtp . '" vs Session: "' . $sessionOtpStr . '"');
            log_message('error', 'Phone Mismatch - Input: "' . $inputPhone . '" vs Session: "' . $sessionPhoneStr . '"');
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'Kode OTP salah',
                'debug' => 'OTP: ' . $inputOtp . ' vs ' . $sessionOtpStr . ' | Phone: ' . $inputPhone . ' vs ' . $sessionPhoneStr
            ]);
        }

        // OTP valid - set flag di session
        $this->session->set('otp_verified', true);
        $this->session->set('verified_phone', $phone);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'OTP berhasil diverifikasi',
            'phone' => $phone
        ]);
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

        // Cek apakah OTP sudah diverifikasi
        if (!$this->session->get('otp_verified')) {
            return redirect()->to('/auth/forgotPassword')->with('error', 'Silakan verifikasi OTP terlebih dahulu');
        }

        $phone = $this->request->getPost('phone');
        $newPassword = $this->request->getPost('new_password');

        // Cek phone sesuai dengan yang diverifikasi
        $verifiedPhone = $this->session->get('verified_phone');
        if ($phone !== $verifiedPhone) {
            log_message('error', 'Phone mismatch - Input: ' . $phone . ' vs Verified: ' . $verifiedPhone);
            return redirect()->to('/auth/forgotPassword')->with('error', 'Nomor telepon tidak valid');
        }

        // Format alternatif nomor HP (untuk mencari di database)
        $formattedPhone = $this->formatPhoneNumber($phone);
        $altPhone = $phone;
        if (substr($formattedPhone, 0, 2) === '62') {
            $altPhone = '0' . substr($formattedPhone, 2);
        }

        // Cari user berdasarkan nomor HP (cek berbagai format)
        $user = $this->userModel->groupStart()
                                    ->where('phone', $phone)
                                    ->orWhere('phone', $formattedPhone)
                                    ->orWhere('phone', $altPhone)
                                ->groupEnd()
                                ->first();

        if (!$user) {
            log_message('error', 'User not found for phone: ' . $phone . ' (formats: ' . $formattedPhone . ', ' . $altPhone . ')');
            return redirect()->back()->with('error', 'Nomor telepon tidak terdaftar');
        }

        // Update password - JANGAN HASH DI SINI, biar model yang handle!
        $updateData = [
            'password' => $newPassword  // Plain text, model akan auto-hash via beforeUpdate
        ];
        
        log_message('info', 'Attempting to reset password for user ID: ' . $user['id'] . ' | Phone: ' . $phone);

        if ($this->userModel->update($user['id'], $updateData)) {
            log_message('info', 'Password successfully reset for user ID: ' . $user['id'] . ' | Email: ' . $user['email']);
            
            // Clear OTP session
            $this->session->remove(['otp_code', 'otp_phone', 'otp_time', 'otp_verified', 'verified_phone']);
            
            return redirect()->to('/auth/login')->with('success', 'Password berhasil direset! Silakan login dengan password baru.');
        } else {
            log_message('error', 'Failed to update password for user ID: ' . $user['id']);
            return redirect()->back()->with('error', 'Gagal mereset password. Silakan coba lagi.');
        }
    }

    /**
     * TEST: Cek password hash (HAPUS SETELAH TESTING!)
     */
    public function testPassword()
    {
        $email = $this->request->getGet('email');
        $password = $this->request->getGet('password');
        
        if (!$email || !$password) {
            return $this->response->setJSON(['error' => 'Provide email and password in URL']);
        }
        
        $user = $this->userModel->where('email', $email)->first();
        
        if (!$user) {
            return $this->response->setJSON(['error' => 'User not found']);
        }
        
        $isValid = password_verify($password, $user['password']);
        
        return $this->response->setJSON([
            'email' => $email,
            'password_input' => $password,
            'password_hash_db' => $user['password'],
            'is_valid' => $isValid,
            'password_verify_result' => $isValid ? 'MATCH' : 'NOT MATCH'
        ]);
    }

    /**
     * Helper: Format nomor HP ke format internasional
     */
    private function formatPhoneNumber($phone)
    {
        // Hapus semua karakter non-digit
        $phone = preg_replace('/\D/', '', $phone);
        
        // Jika diawali 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            return '62' . substr($phone, 1);
        }
        
        // Jika belum ada kode negara, tambahkan 62
        if (substr($phone, 0, 2) !== '62') {
            return '62' . $phone;
        }
        
        return $phone;
    }
}
