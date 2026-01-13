<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\BookingModel;
use App\Models\PaymentProofModel;
use App\Models\EventModel;
use App\Models\UserModel;

class AdminController extends BaseController
{
    protected $adminModel;
    protected $bookingModel;
    protected $paymentProofModel;
    protected $eventModel;
    protected $userModel;

    public function __construct()
    {
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
        // Statistik
        $stats = [
            'total_bookings' => $this->bookingModel->countAll(),
            'pending_payments' => $this->bookingModel->where('status', 'Waiting Approval')->countAllResults(),
            'confirmed_bookings' => $this->bookingModel->where('status', 'Confirmed')->countAllResults(),
            'total_revenue' => $this->bookingModel
                ->selectSum('total_price')
                ->where('status', 'Confirmed')
                ->first()['total_price'] ?? 0,
            'total_users' => $this->userModel->countAll(),
            'total_events' => $this->eventModel->countAll()
        ];

        // Recent bookings
        $recentBookings = $this->bookingModel
            ->orderBy('booking_date', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'title' => 'Admin Dashboard - EventKu',
            'admin' => $this->getAdminData(),
            'stats' => $stats,
            'recentBookings' => $recentBookings
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Daftar Semua Booking
     */
    public function bookings()
    {
        // Filter status
        $status = $this->request->getGet('status');

        $builder = $this->bookingModel->orderBy('booking_date', 'DESC');

        if ($status) {
            $builder->where('status', $status);
        }

        $bookings = $builder->findAll();

        $data = [
            'title' => 'Manage Bookings - EventKu',
            'admin' => $this->getAdminData(),
            'bookings' => $bookings,
            'current_status' => $status
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

        if ($booking['status'] !== 'Waiting Approval') {
            return redirect()->back()->with('error', 'Booking tidak dalam status Waiting Approval');
        }

        // Update booking status
        $updateData = [
            'status' => 'Confirmed',
            'payment_confirmed_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->bookingModel->update($bookingId, $updateData);

            // TODO: Send confirmation email/WhatsApp di Fase 3

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

        if ($booking['status'] !== 'Waiting Approval') {
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
                'status' => 'Cancelled',
                'payment_details' => 'Ditolak: ' . $rejectReason,
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

            // TODO: Send notification email/WhatsApp di Fase 3

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
        $events = $this->eventModel->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title' => 'Manage Events - EventKu',
            'admin' => $this->getAdminData(),
            'events' => $events
        ];

        return view('admin/events', $data);
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
            'date' => 'required|valid_date',
            'location' => 'required',
            'price' => 'required|numeric',
            'category' => 'required',
            'available_tickets' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $eventId = $this->request->getPost('event_id');

        $eventData = [
            'title' => $this->request->getPost('title'),
            'date' => $this->request->getPost('date'),
            'location' => $this->request->getPost('location'),
            'price' => $this->request->getPost('price'),
            'category' => $this->request->getPost('category'),
            'icon' => $this->request->getPost('icon') ?? '🎉',
            'description' => $this->request->getPost('description'),
            'available_tickets' => $this->request->getPost('available_tickets'),
            'is_active' => $this->request->getPost('is_active') ?? 1
        ];

        // Handle image upload (optional)
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $newImageName = 'event_' . time() . '.' . $imageFile->getExtension();
            $imageFile->move(FCPATH . 'uploads/events', $newImageName);
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

        $data = [
            'title' => 'Manage Users - EventKu',
            'admin' => $this->getAdminData(),
            'users' => $users
        ];

        return view('admin/users', $data);
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
}
