<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\BookingModel;
use App\Models\UserModel;

class UserController extends BaseController
{
    protected $eventModel;
    protected $bookingModel;
    protected $userModel;

    public function __construct()
    {
        $this->eventModel = new EventModel();
        $this->bookingModel = new BookingModel();
        $this->userModel = new UserModel();
    }

    /**
     * Dashboard - Tampilkan daftar events aktif
     */
    public function dashboard()
    {
        $data = [
            'title' => 'Dashboard - EventKu',
            'user' => $this->getUserData(),
            'events' => $this->eventModel->getActiveEvents()
        ];

        return view('user/dashboard', $data);
    }

    /**
     * Halaman Booking Detail Event
     */
    public function booking($eventId)
    {
        $event = $this->eventModel->find($eventId);

        if (!$event) {
            return redirect()->to('/user/dashboard')->with('error', 'Event tidak ditemukan');
        }

        if (!$event['is_active']) {
            return redirect()->to('/user/dashboard')->with('error', 'Event tidak aktif');
        }

        if ($event['available_tickets'] <= 0) {
            return redirect()->to('/user/dashboard')->with('error', 'Tiket sudah habis');
        }

        $data = [
            'title' => 'Booking - ' . $event['title'],
            'user' => $this->getUserData(),
            'event' => $event,
            'validation' => \Config\Services::validation()
        ];

        return view('user/booking', $data);
    }

    /**
     * Proses Booking (POST)
     */
    public function processBooking()
    {
        // Validasi input
        $rules = [
            'event_id' => 'required|numeric',
            'ticket_count' => 'required|numeric|greater_than[0]',
            'payment_method' => 'required|in_list[midtrans,manual_transfer]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $eventId = $this->request->getPost('event_id');
        $ticketCount = $this->request->getPost('ticket_count');
        $paymentMethod = $this->request->getPost('payment_method');

        // Get event data
        $event = $this->eventModel->find($eventId);

        if (!$event) {
            return redirect()->to('/user/dashboard')->with('error', 'Event tidak ditemukan');
        }

        // Cek ketersediaan tiket
        if ($event['available_tickets'] < $ticketCount) {
            return redirect()->back()->with('error', 'Tiket tidak mencukupi. Tersedia: ' . $event['available_tickets']);
        }

        // Hitung total harga
        $totalPrice = $event['price'] * $ticketCount;

        // Generate booking number
        $bookingNumber = $this->bookingModel->generateBookingNumber();

        // Prepare booking data
        $bookingData = [
            'booking_number' => $bookingNumber,
            'user_id' => $this->session->get('user_id'),
            'event_id' => $eventId,
            'event_title' => $event['title'],
            'event_date' => $event['date'],
            'event_location' => $event['location'],
            'event_icon' => $event['icon'],
            'ticket_count' => $ticketCount,
            'price_per_ticket' => $event['price'],
            'total_price' => $totalPrice,
            'payment_method' => $paymentMethod,
            'status' => $paymentMethod === 'midtrans' ? \App\Config\BookingStatus::WAITING_PAYMENT : \App\Config\BookingStatus::PENDING,
            'expired_at' => date('Y-m-d H:i:s', strtotime('+24 hours'))
        ];

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert booking
            $bookingId = $this->bookingModel->insert($bookingData);

            // Update available tickets
            $newAvailableTickets = $event['available_tickets'] - $ticketCount;
            $this->eventModel->update($eventId, ['available_tickets' => $newAvailableTickets]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Booking gagal, silakan coba lagi');
            }

            // Redirect based on payment method
            if ($paymentMethod === 'midtrans') {
                return redirect()->to('/payment/midtrans/' . $bookingId)->with('success', 'Booking berhasil! Silakan lanjutkan pembayaran');
            }

            return redirect()->to('/payment/manual/' . $bookingId)->with('success', 'Booking berhasil! Silakan transfer dan upload bukti pembayaran.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Booking gagal: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Riwayat Booking
     */
    public function riwayat()
    {
        $userId = $this->session->get('user_id');
        $bookings = $this->bookingModel->getUserBookings($userId);

        $data = [
            'title' => 'Riwayat Booking - EventKu',
            'user' => $this->getUserData(),
            'bookings' => $bookings
        ];

        return view('user/riwayat', $data);
    }

    /**
     * Halaman Profile
     */
    public function profile()
    {
        $data = [
            'title' => 'Profile - EventKu',
            'user' => $this->getUserData(),
            'validation' => \Config\Services::validation()
        ];

        return view('user/profile', $data);
    }

    /**
     * Update Profile (POST)
     */
    public function updateProfile()
    {
        $userId = $this->session->get('user_id');

        // Validasi input
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'phone' => 'required|min_length[10]|max_length[20]',
            'email' => 'required|valid_email|is_unique[users.email,id,' . $userId . ']'
        ];

        // Jika password diisi, validate
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
            $rules['password_confirm'] = 'matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare data
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone')
        ];

        // Tambahkan password jika diisi
        if ($this->request->getPost('password')) {
            $data['password'] = $this->request->getPost('password');
        }

        try {
            $this->userModel->update($userId, $data);

            // Update session name & email
            $this->session->set([
                'name' => $data['name'],
                'email' => $data['email']
            ]);

            return redirect()->back()->with('success', 'Profile berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Update profile gagal: ' . $e->getMessage());
        }
    }

    /**
     * Cancel Booking
     */
    public function cancelBooking($bookingId)
    {
        $userId = $this->session->get('user_id');
        $booking = $this->bookingModel->find($bookingId);

        // Validasi
        if (!$booking) {
            return redirect()->to('/user/riwayat')->with('error', 'Booking tidak ditemukan');
        }

        if ($booking['user_id'] != $userId) {
            return redirect()->to('/user/riwayat')->with('error', 'Unauthorized');
        }

        if (!in_array($booking['status'], ['Pending', 'Waiting Payment'])) {
            return redirect()->to('/user/riwayat')->with('error', 'Booking tidak bisa dibatalkan');
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update booking status
            $this->bookingModel->update($bookingId, [
                'status' => 'Cancelled',
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
                return redirect()->back()->with('error', 'Pembatalan gagal');
            }

            return redirect()->to('/user/riwayat')->with('success', 'Booking berhasil dibatalkan');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Pembatalan gagal: ' . $e->getMessage());
        }
    }

    /**
     * Get User Data dari Database
     */
    private function getUserData()
    {
        $userId = $this->session->get('user_id');
        return $this->userModel->find($userId);
    }
}
