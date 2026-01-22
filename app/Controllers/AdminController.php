<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\BookingModel;
use App\Models\PaymentProofModel;
use App\Models\EventModel;
use App\Models\UserModel;
use App\Config\BookingStatus;

class AdminController extends BaseController
{
    protected $adminModel;
    protected $bookingModel;
    protected $paymentProofModel;
    protected $eventModel;
    protected $userModel;

    public function __construct()
    {
         helper('admin');
        $this->adminModel = new AdminModel();
        $this->bookingModel = new BookingModel();
        $this->paymentProofModel = new PaymentProofModel();
        $this->eventModel = new EventModel();
        $this->userModel = new UserModel();
    }








    /**
     * Admin Login Page
     */
    
    public function login()
    {
        // Jika sudah login, redirect ke dashboard
        if ($this->session->get('admin_logged_in')) {
            return redirect()->to('/admin/dashboard');
        }

        $data = [
            'title' => 'Admin Login - EventKu',
            'validation' => \Config\Services::validation()
        ];

        return view('admin/login', $data);
    }

    /**
     * Proses Admin Login (POST)
     */
    public function doLogin()
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

        // Cek admin di database
        $admin = $this->adminModel->where('username', $username)->first();

        if (!$admin) {
            return redirect()->back()->withInput()->with('error', 'Username tidak ditemukan');
        }

        // Verifikasi password
        if (!password_verify($password, $admin['password'])) {
            return redirect()->back()->withInput()->with('error', 'Password salah');
        }

        // Cek apakah admin aktif
        if (!$admin['is_active']) {
            return redirect()->back()->with('error', 'Akun admin tidak aktif');
        }

        // Set session
        $sessionData = [
            'admin_id' => $admin['id'],
            'admin_username' => $admin['username'],
            'admin_email' => $admin['email'],
            'admin_full_name' => $admin['full_name'],
            'admin_role' => $admin['role'],
            'admin_logged_in' => true
        ];

        $this->session->set($sessionData);

        // Update last login
        $this->adminModel->update($admin['id'], ['last_login' => date('Y-m-d H:i:s')]);

        return redirect()->to('/admin/dashboard')->with('success', 'Login berhasil! Selamat datang ' . $admin['full_name']);
    }

    /**
     * Admin Dashboard
     */
   public function dashboard()
{
    $stats = [
        // TOTAL
        'total_events' => $this->eventModel->countAll(),
        'total_bookings' => $this->bookingModel->countAll(),
        'pending_payments' => $this->bookingModel
            ->where('status', BookingStatus::WAITING_APPROVAL)
            ->countAllResults(),
        'confirmed_bookings' => $this->bookingModel
            ->where('status', BookingStatus::LUNAS)
            ->countAllResults(),
        'total_revenue' => $this->bookingModel
            ->selectSum('total_price')
            ->where('status', BookingStatus::LUNAS)
            ->first()['total_price'] ?? 0,
        'total_users' => $this->userModel->countAll(),
        'active_users' => $this->userModel->countAll(), // Sama dengan total_users karena tidak ada kolom status

        // 📈 ANALYTICS (ini yang bikin admin kamu KEREN)
        'events_change' => $this->calculateMonthlyChange('events'),
        'bookings_change' => $this->calculateMonthlyChange('bookings'),
        'users_change' => $this->calculateMonthlyChange('users'),
        'revenue_change' => $this->calculateRevenueChange()
    ];

    // Recent bookings (siap tabel UI)
    $recentBookings = $this->bookingModel
        ->select('bookings.*, users.name AS customer_name')
        ->join('users', 'users.id = bookings.user_id')
        ->orderBy('booking_date', 'DESC')
        ->limit(10)
        ->findAll();

    return view('admin/dashboard', [
        'title' => 'Admin Dashboard - EventKu',
        'admin' => $this->getAdminData(),
        'stats' => $stats,
        'recentBookings' => $recentBookings
    ]);
}

    /**
     * Daftar Semua Booking
     */
    public function bookings()
    {
        // Filter status
        $status = $this->request->getGet('status');

        $builder = $this->bookingModel
            ->select('bookings.*, users.name AS customer_name')
            ->join('users', 'users.id = bookings.user_id', 'left')
            ->orderBy('booking_date', 'DESC');

        if ($status) {
            $builder->where('bookings.status', $status);
        }

        $bookings = $builder->findAll();

        // Get pending payments count for badge
        $stats = [
            'pending_payments' => $this->bookingModel
                ->where('status', BookingStatus::WAITING_APPROVAL)
                ->countAllResults()
        ];

        $data = [
            'title' => 'Manage Bookings - EventKu',
            'admin' => $this->getAdminData(),
            'bookings' => $bookings,
            'current_status' => $status,
            'stats' => $stats
        ];

        return view('admin/bookings', $data);
    }

    /**
     * Approve Payment
     */
    public function approvePayment($bookingId)
    {
        $booking = $this->bookingModel->find($bookingId);

        if (!$booking) {
            return redirect()->back()->with('error', 'Booking tidak ditemukan');
        }

        if ($booking['status'] !== BookingStatus::WAITING_APPROVAL) {
            return redirect()->back()->with('error', 'Booking tidak dalam status Waiting Approval');
        }

        // Update booking status
        $updateData = [
            'status' => BookingStatus::LUNAS,
            'payment_confirmed_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->bookingModel->update($bookingId, $updateData);

            return redirect()->back()->with('success', 'Pembayaran berhasil diapprove! Booking #' . $booking['booking_number']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Approve gagal: ' . $e->getMessage());
        }
    }

    /**
     * Reject Payment
     */
    public function rejectPayment($bookingId)
    {
        $booking = $this->bookingModel->find($bookingId);

        if (!$booking) {
            return redirect()->back()->with('error', 'Booking tidak ditemukan');
        }

        if ($booking['status'] !== BookingStatus::WAITING_APPROVAL) {
            return redirect()->back()->with('error', 'Booking tidak dalam status Waiting Approval');
        }

        // Get reject reason
        $rejectReason = $this->request->getPost('reject_reason') ?? 'Bukti transfer tidak valid';

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update booking status
            $this->bookingModel->update($bookingId, [
                'status' => BookingStatus::DIBATALKAN,
                'payment_details' => json_encode([
                    'type' => 'manual_transfer_rejected',
                    'reason' => $rejectReason,
                    'at' => date('Y-m-d H:i:s'),
                ], JSON_UNESCAPED_UNICODE),
                'cancelled_at' => date('Y-m-d H:i:s')
            ]);

            // Kembalikan available tickets
            $event = $this->eventModel->find($booking['event_id']);
            if ($event) {
                $newAvailableTickets = $event['available_tickets'] + $booking['ticket_count'];
                $this->eventModel->update($booking['event_id'], ['available_tickets' => $newAvailableTickets]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Reject gagal');
            }

            return redirect()->back()->with('success', 'Pembayaran ditolak. Booking #' . $booking['booking_number']);
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Reject gagal: ' . $e->getMessage());
        }
    }

    /**
     * View Payment Proof
     */
    public function viewPaymentProof($bookingId)
    {
        $booking = $this->bookingModel->find($bookingId);
        $paymentProof = $this->paymentProofModel->where('booking_id', $bookingId)->first();

        if (!$booking || !$paymentProof) {
            return redirect()->back()->with('error', 'Bukti pembayaran tidak ditemukan');
        }

        $data = [
            'title' => 'Bukti Pembayaran - EventKu',
            'admin' => $this->getAdminData(),
            'booking' => $booking,
            'paymentProof' => $paymentProof
        ];

        return view('admin/payment_proof', $data);
    }

    /**
     * Manage Events (CRUD)
     */
    public function events()
    {
        // Filter berdasarkan status (upcoming/past/all)
        $filter = $this->request->getGet('filter') ?? 'all';
        
        switch ($filter) {
            case 'upcoming':
                $events = $this->eventModel->getUpcomingEvents();
                break;
            case 'past':
                $events = $this->eventModel->getPastEvents();
                break;
            default:
                $events = $this->eventModel->orderBy('date', 'DESC')->findAll();
                break;
        }

        // Hitung statistik  
        $today = date('Y-m-d');
        
        $totalEvents = $this->eventModel->countAll();
        
        // Clone builder untuk upcoming count
        $upcomingCount = $this->eventModel->where('date >=', $today)->countAllResults(false);
        
        // Buat instance baru untuk past count
        $eventModelForPast = new \App\Models\EventModel();
        $pastCount = $eventModelForPast->where('date <', $today)->countAllResults();
        
        $stats = [
            'total' => $totalEvents,
            'upcoming' => $upcomingCount,
            'past' => $pastCount
        ];

        $data = [
            'title' => 'Manage Events - EventKu',
            'admin' => $this->getAdminData(),
            'events' => $events,
            'filter' => $filter,
            'stats' => $stats
        ];

        return view('admin/events', $data);
    }
    
    /**
     * Archive Past Events (manual trigger)
     */
    public function archivePastEvents()
    {
        $count = $this->eventModel->autoArchivePastEvents();
        
        return redirect()->to('/admin/events')->with('success', "Berhasil mengarsipkan {$count} event yang sudah lewat");
    }

    /**
     * Create Event
     */
    public function createEvent()
    {
        $data = [
            'title' => 'Create Event - EventKu',
            'admin' => $this->getAdminData(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/event_form', $data);
    }

    /**
     * Edit Event
     */
    public function editEvent($eventId)
    {
        $event = $this->eventModel->find($eventId);

        if (!$event) {
            return redirect()->to('/admin/events')->with('error', 'Event tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Event - EventKu',
            'admin' => $this->getAdminData(),
            'event' => $event,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/event_form', $data);
    }

    /**
     * Save Event (Create/Update)
     */
    public function saveEvent()
    {
        // Validasi input
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'date' => 'required',
            'location' => 'required',
            'price' => 'required|numeric',
            'category' => 'required',
            'available_tickets' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $eventId = $this->request->getPost('event_id');

        // Konversi format tanggal ke YYYY-MM-DD
        $dateInput = $this->request->getPost('date');
        $dateFormatted = $dateInput;
        
        // Cek apakah format sudah YYYY-MM-DD atau perlu konversi
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateInput)) {
            // Coba parse berbagai format tanggal
            try {
                $dateObj = new \DateTime($dateInput);
                $dateFormatted = $dateObj->format('Y-m-d');
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Format tanggal tidak valid. Gunakan format YYYY-MM-DD atau tanggal yang valid.');
            }
        }

        $eventData = [
            'title' => $this->request->getPost('title'),
            'date' => $dateFormatted,
            'location' => $this->request->getPost('location'),
            'price' => $this->request->getPost('price'),
            'category' => $this->request->getPost('category'),
            'icon' => $this->request->getPost('icon') ?? '🎉',
            'description' => $this->request->getPost('description'),
            'available_tickets' => $this->request->getPost('available_tickets'),
            'is_active' => $this->request->getPost('is_active') ?? 1
        ];

        // Handle image upload
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            // Create uploads directory if not exists
            $uploadPath = FCPATH . 'uploads/events';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $newImageName = 'event_' . time() . '.' . $imageFile->getExtension();
            $imageFile->move($uploadPath, $newImageName);
            $eventData['image'] = 'uploads/events/' . $newImageName;
        }

        try {
            if ($eventId) {
                // Update existing event
                $this->eventModel->update($eventId, $eventData);
                $message = 'Event berhasil diupdate';
            } else {
                // Create new event
                $this->eventModel->insert($eventData);
                $message = 'Event berhasil dibuat';
            }

            return redirect()->to('/admin/events')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan event: ' . $e->getMessage());
        }
    }

    /**
     * Delete Event
     */
    public function deleteEvent($eventId)
    {
        $event = $this->eventModel->find($eventId);

        if (!$event) {
            return redirect()->back()->with('error', 'Event tidak ditemukan');
        }

        // Cek apakah ada booking untuk event ini
        $bookingCount = $this->bookingModel->where('event_id', $eventId)->countAllResults();

        if ($bookingCount > 0) {
            return redirect()->back()->with('error', 'Event tidak bisa dihapus karena sudah ada booking');
        }

        try {
            $this->eventModel->delete($eventId);
            return redirect()->to('/admin/events')->with('success', 'Event berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus event: ' . $e->getMessage());
        }
    }

    /**
     * Manage Users
     */
    public function users()
    {
        $users = $this->userModel->orderBy('registered_at', 'DESC')->findAll();

        // Precompute booking counts to avoid N+1 queries in the view
        $bookingCounts = [];
        $counts = $this->bookingModel
            ->select('user_id, COUNT(*) AS total')
            ->groupBy('user_id')
            ->findAll();

        foreach ($counts as $row) {
            $bookingCounts[(int) $row['user_id']] = (int) $row['total'];
        }

        $stats = [
            'pending_payments' => $this->bookingModel
                ->where('status', BookingStatus::WAITING_APPROVAL)
                ->countAllResults(),
        ];

        $data = [
            'title' => 'Manage Users - EventKu',
            'admin' => $this->getAdminData(),
            'users' => $users,
            'bookingCounts' => $bookingCounts,
            'stats' => $stats
        ];

        return view('admin/users', $data);
    }

    /**
     * User Detail
     */
    public function userDetail($userId)
    {
        $userId = (int) $userId;
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }

        $bookings = $this->bookingModel
            ->where('user_id', $userId)
            ->orderBy('booking_date', 'DESC')
            ->findAll();

        $totalSpent = 0;
        $confirmed = 0;
        $pending = 0;
        $cancelled = 0;

        foreach ($bookings as $booking) {
            if ($booking['status'] === BookingStatus::LUNAS) {
                $confirmed++;
                $totalSpent += (int) ($booking['total_price'] ?? 0);
            } elseif (in_array($booking['status'], [BookingStatus::PENDING, BookingStatus::WAITING_PAYMENT, BookingStatus::WAITING_APPROVAL], true)) {
                $pending++;
            } elseif (in_array($booking['status'], [BookingStatus::DIBATALKAN, BookingStatus::EXPIRED], true)) {
                $cancelled++;
            }
        }

        $stats = [
            'pending_payments' => $this->bookingModel
                ->where('status', BookingStatus::WAITING_APPROVAL)
                ->countAllResults(),
        ];

        $userStats = [
            'total' => count($bookings),
            'confirmed' => $confirmed,
            'pending' => $pending,
            'cancelled' => $cancelled,
            'total_spent' => $totalSpent,
        ];

        return view('admin/user_detail', [
            'title' => 'Detail User - EventKu',
            'admin' => $this->getAdminData(),
            'user' => $user,
            'bookings' => $bookings,
            'userStats' => $userStats,
            'stats' => $stats,
        ]);
    }

    /**
     * Manage Payments
     */
    public function payments()
    {
        $status = $this->request->getGet('status');
        $method = $this->request->getGet('method');

        $builder = $this->bookingModel
            ->select('bookings.*, users.name AS customer_name')
            ->join('users', 'users.id = bookings.user_id', 'left')
            ->orderBy('booking_date', 'DESC');

        if (!empty($status)) {
            $builder->where('bookings.status', $status);
        }
        if (!empty($method)) {
            $builder->where('bookings.payment_method', $method);
        }

        $payments = $builder->findAll();

        $stats = [
            'pending_payments' => $this->bookingModel
                ->where('status', BookingStatus::WAITING_APPROVAL)
                ->countAllResults(),
            'waiting_payment' => $this->bookingModel
                ->where('status', BookingStatus::WAITING_PAYMENT)
                ->countAllResults(),
            'paid' => $this->bookingModel
                ->where('status', BookingStatus::LUNAS)
                ->countAllResults(),
            'total_revenue' => $this->bookingModel
                ->selectSum('total_price')
                ->where('status', BookingStatus::LUNAS)
                ->first()['total_price'] ?? 0,
        ];

        return view('admin/payments', [
            'title' => 'Manage Payments - EventKu',
            'admin' => $this->getAdminData(),
            'payments' => $payments,
            'current_status' => $status,
            'current_method' => $method,
            'stats' => $stats,
        ]);
    }

    /**
     * Settings
     */
    public function settings()
    {
        $stats = [
            'pending_payments' => $this->bookingModel
                ->where('status', BookingStatus::WAITING_APPROVAL)
                ->countAllResults(),
        ];

        return view('admin/settings', [
            'title' => 'Settings - EventKu',
            'admin' => $this->getAdminData(),
            'stats' => $stats,
        ]);
    }

    /**
     * Update admin profile (name + email)
     */
    public function updateProfile()
    {
        $adminId = (int) $this->session->get('admin_id');
        if (!$adminId) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login kembali');
        }

        $rules = [
            'full_name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email|is_unique[admins.email,id,' . $adminId . ']',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fullName = (string) $this->request->getPost('full_name');
        $email = (string) $this->request->getPost('email');

        try {
            $this->adminModel->update($adminId, [
                'full_name' => $fullName,
                'email' => $email,
            ]);

            $this->session->set([
                'admin_full_name' => $fullName,
                'admin_email' => $email,
            ]);

            return redirect()->to('/admin/settings')->with('success', 'Profil berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal update profil: ' . $e->getMessage());
        }
    }

    /**
     * Change admin password
     */
    public function changePassword()
    {
        $adminId = (int) $this->session->get('admin_id');
        if (!$adminId) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login kembali');
        }

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $admin = $this->adminModel->find($adminId);
        if (!$admin) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login kembali');
        }

        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword = (string) $this->request->getPost('new_password');

        if (!password_verify($currentPassword, $admin['password'])) {
            return redirect()->back()->withInput()->with('error', 'Password saat ini salah');
        }

        try {
            $this->adminModel->update($adminId, ['password' => $newPassword]);
            return redirect()->to('/admin/settings')->with('success', 'Password berhasil diganti');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal ganti password: ' . $e->getMessage());
        }
    }

    /**
     * Admin Logout
     */
    public function logout()
    {
        // Destroy only admin session
        $this->session->remove(['admin_id', 'admin_username', 'admin_email', 'admin_full_name', 'admin_role', 'admin_logged_in']);

        return redirect()->to('/admin/login')->with('success', 'Logout berhasil');
    }

    /**
     * Get Admin Data dari Session
     */
    private function getAdminData()
    {
        return [
            'id' => $this->session->get('admin_id'),
            'username' => $this->session->get('admin_username'),
            'email' => $this->session->get('admin_email'),
            'full_name' => $this->session->get('admin_full_name'),
            'role' => $this->session->get('admin_role')
        ];
    }



  private function calculateMonthlyChange($type)
{
    if ($type === 'events') {
        $model = $this->eventModel;
        $dateField = 'created_at';
    } elseif ($type === 'bookings') {
        $model = $this->bookingModel;
        $dateField = 'booking_date';
    } elseif ($type === 'users') {
        $model = $this->userModel;
        $dateField = 'registered_at';
    } else {
        return 0;
    }

    $current = $model
        ->where('MONTH('.$dateField.')', date('m'))
        ->where('YEAR('.$dateField.')', date('Y'))
        ->countAllResults();

    $last = $model
        ->where('MONTH('.$dateField.')', date('m', strtotime('-1 month')))
        ->where('YEAR('.$dateField.')', date('Y', strtotime('-1 month')))
        ->countAllResults();

    if ($last == 0) return 0;

    return round((($current - $last) / $last) * 100, 1);
}

private function calculateRevenueChange()
{
    $current = $this->bookingModel
        ->selectSum('total_price')
        ->where('status', BookingStatus::LUNAS)
        ->where('MONTH(booking_date)', date('m'))
        ->where('YEAR(booking_date)', date('Y'))
        ->first()['total_price'] ?? 0;

    $last = $this->bookingModel
        ->selectSum('total_price')
        ->where('status', BookingStatus::LUNAS)
        ->where('MONTH(booking_date)', date('m', strtotime('-1 month')))
        ->where('YEAR(booking_date)', date('Y', strtotime('-1 month')))
        ->first()['total_price'] ?? 0;

    if ($last == 0) return 0;

    return round((($current - $last) / $last) * 100, 1);
}

}