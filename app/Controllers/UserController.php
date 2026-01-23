<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\BookingModel;
use App\Models\UserModel;
use App\Models\FavoriteModel;

class UserController extends BaseController
{
    protected $eventModel;
    protected $bookingModel;
    protected $userModel;
    protected $favoriteModel;

    public function __construct()
    {
        $this->eventModel = new EventModel();
        $this->bookingModel = new BookingModel();
        $this->userModel = new UserModel();
        $this->favoriteModel = new FavoriteModel();
    }




     /**
     * Halaman: Dashboard Statistics
     */
    public function statistics()
    {
        $userId = $this->session->get('user_id');
        
        // Get all user bookings
        $allBookings = $this->bookingModel->where('user_id', $userId)->findAll();
        
        // Calculate statistics
        $stats = [
            'total_bookings' => count($allBookings),
            'total_tickets' => array_sum(array_column($allBookings, 'ticket_count')),
            'total_spending' => array_sum(array_column(
                array_filter($allBookings, fn($b) => $b['status'] === 'Lunas'),
                'total_price'
            )),
            'confirmed_bookings' => count(array_filter($allBookings, fn($b) => $b['status'] === 'Lunas')),
            'pending_bookings' => count(array_filter($allBookings, fn($b) => in_array($b['status'], ['Pending', 'Waiting Payment', 'Waiting Approval']))),
            'cancelled_bookings' => count(array_filter($allBookings, fn($b) => in_array($b['status'], ['Dibatalkan', 'Expired']))),
        ];
        
        // Booking Timeline (Last 6 months)
        $bookingTimeline = $this->getBookingTimeline($userId);
        
        // Category Distribution
        $categoryDistribution = $this->getCategoryDistribution($userId);
        
        // Monthly Spending
        $monthlySpending = $this->getMonthlySpending($userId);
        
        // Top Events
        $topEvents = $this->getTopEvents($userId);
        
        // Payment Method Distribution
        $paymentMethods = $this->getPaymentMethodDistribution($userId);
        
        $data = [
            'title' => 'Statistics - EventKu',
            'stats' => $stats,
            'bookingTimeline' => $bookingTimeline,
            'categoryDistribution' => $categoryDistribution,
            'monthlySpending' => $monthlySpending,
            'topEvents' => $topEvents,
            'paymentMethods' => $paymentMethods
        ];

        return view('user/statistics', $data);
    }

    /**
     * Get Booking Timeline (Last 6 months)
     */
    private function getBookingTimeline($userId)
    {
        $bookings = $this->bookingModel
            ->where('user_id', $userId)
            ->where('booking_date >=', date('Y-m-d', strtotime('-6 months')))
            ->orderBy('booking_date', 'ASC')
            ->findAll();
        
        // Group by month
        $timeline = [];
        foreach ($bookings as $booking) {
            $month = date('M Y', strtotime($booking['booking_date']));
            if (!isset($timeline[$month])) {
                $timeline[$month] = [
                    'total' => 0,
                    'confirmed' => 0,
                    'pending' => 0,
                    'cancelled' => 0
                ];
            }
            
            $timeline[$month]['total']++;
            
            if ($booking['status'] === 'Lunas') {
                $timeline[$month]['confirmed']++;
            } elseif (in_array($booking['status'], ['Pending', 'Waiting Payment', 'Waiting Approval'])) {
                $timeline[$month]['pending']++;
            } else {
                $timeline[$month]['cancelled']++;
            }
        }
        
        return $timeline;
    }

    /**
     * Get Category Distribution
     */
    private function getCategoryDistribution($userId)
    {
        $bookings = $this->bookingModel
            ->select('bookings.*, events.category')
            ->join('events', 'events.id = bookings.event_id')
            ->where('bookings.user_id', $userId)
            ->where('bookings.status', 'Lunas')
            ->findAll();
        
        $categories = [];
        foreach ($bookings as $booking) {
            $category = $booking['category'] ?? 'Lainnya';
            if (!isset($categories[$category])) {
                $categories[$category] = 0;
            }
            $categories[$category]++;
        }
        
        arsort($categories);
        return $categories;
    }

    /**
     * Get Monthly Spending (Last 6 months)
     */
    private function getMonthlySpending($userId)
    {
        $bookings = $this->bookingModel
            ->where('user_id', $userId)
            ->where('status', 'Lunas')
            ->where('payment_confirmed_at >=', date('Y-m-d', strtotime('-6 months')))
            ->orderBy('payment_confirmed_at', 'ASC')
            ->findAll();
        
        $spending = [];
        foreach ($bookings as $booking) {
            $month = date('M Y', strtotime($booking['payment_confirmed_at']));
            if (!isset($spending[$month])) {
                $spending[$month] = 0;
            }
            $spending[$month] += $booking['total_price'];
        }
        
        return $spending;
    }

    /**
     * Get Top Events (Most booked)
     */
    private function getTopEvents($userId)
    {
        $bookings = $this->bookingModel
            ->select('event_title, COUNT(*) as booking_count, SUM(total_price) as total_spent')
            ->where('user_id', $userId)
            ->where('status', 'Lunas')
            ->groupBy('event_title')
            ->orderBy('booking_count', 'DESC')
            ->limit(5)
            ->findAll();
        
        return $bookings;
    }

    /**
     * Get Payment Method Distribution
     */
    private function getPaymentMethodDistribution($userId)
    {
        $bookings = $this->bookingModel
            ->where('user_id', $userId)
            ->where('status', 'Lunas')
            ->findAll();
        
        $methods = [
            'manual_transfer' => 0,
            'midtrans' => 0
        ];
        
        foreach ($bookings as $booking) {
            if (isset($methods[$booking['payment_method']])) {
                $methods[$booking['payment_method']]++;
            }
        }
        
        return $methods;
    }


     public function toggleFavorite()
    {
        // Cek login
        if (!$this->session->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ]);
        }

        $eventId = $this->request->getPost('event_id');
        $userId = $this->session->get('user_id');

        if (!$eventId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Event ID tidak valid'
            ]);
        }

        // Cek apakah event ada
        $event = $this->eventModel->find($eventId);
        if (!$event) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Event tidak ditemukan'
            ]);
        }

        // Toggle favorite
        $result = $this->favoriteModel->toggleFavorite($userId, $eventId);
        
        $message = $result['action'] === 'added' 
            ? 'Event ditambahkan ke favorit!' 
            : 'Event dihapus dari favorit';

        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'favorited' => $result['favorited'],
            'action' => $result['action']
        ]);
    }

    /**
     * Halaman: My Favorites
     */
    public function favorites()
    {
        $userId = $this->session->get('user_id');
        
        // Ambil semua event favorit user
        $favorites = $this->favoriteModel->getUserFavorites($userId);
        
        $data = [
            'title' => 'Event Favorit - EventKu',
            'favorites' => $favorites,
            'totalFavorites' => count($favorites)
        ];

        return view('user/favorites', $data);
    }



    /**
     * Dashboard - Tampilkan daftar events aktif
     */
    public function dashboard()
    {
        // Ambil favorite IDs user
        $favoriteIds = [];
        if ($this->session->get('logged_in')) {
            $userId = $this->session->get('user_id');
            $favoriteIds = $this->favoriteModel->getUserFavoriteIds($userId);
        }


        $data = [
            'title' => 'Dashboard - EventKu',
            'user' => $this->getUserData(),
            'events' => $this->eventModel->getActiveEvents(),
            'favoriteIds' => $favoriteIds
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
            'expired_at' => date('Y-m-d H:i:s', strtotime('+12 hours')),
            'payment_deadline' => $paymentMethod === 'manual_transfer' ? date('Y-m-d H:i:s', strtotime('+24 hours')) : null
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


    public function submitRating()
{
    if (!$this->session->get('logged_in')) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Silakan login terlebih dahulu'
        ]);
    }

    $ratingModel = new \App\Models\RatingModel();
    $userId = $this->session->get('user_id');
    $eventId = $this->request->getPost('event_id');
    $rating = $this->request->getPost('rating');
    $review = $this->request->getPost('review');
    $isAnonymous = $this->request->getPost('is_anonymous') ? 1 : 0;

    // Validate
    if (!$eventId || !$rating) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Data tidak lengkap'
        ]);
    }

    // Check if user can rate
    $canRate = $ratingModel->canUserRate($userId, $eventId);
    if (!$canRate['can_rate']) {
        return $this->response->setJSON([
            'success' => false,
            'message' => $canRate['reason']
        ]);
    }

    // Get booking ID
    $booking = $this->bookingModel
        ->where('user_id', $userId)
        ->where('event_id', $eventId)
        ->where('status', 'Lunas')
        ->first();

    $data = [
        'event_id' => $eventId,
        'user_id' => $userId,
        'booking_id' => $booking ? $booking['id'] : null,
        'rating' => $rating,
        'review' => $review,
        'is_anonymous' => $isAnonymous
    ];

    if ($ratingModel->addRating($data)) {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Rating berhasil dikirim!'
        ]);
    }

    return $this->response->setJSON([
        'success' => false,
        'message' => 'Gagal mengirim rating'
    ]);
}

/**
 * Get Event Ratings (AJAX)
 */
public function getEventRatings($eventId)
{
    $ratingModel = new \App\Models\RatingModel();
    
    $ratings = $ratingModel->getEventRatings($eventId);
    $stats = $ratingModel->getRatingStats($eventId);
    
    return $this->response->setJSON([
        'success' => true,
        'ratings' => $ratings,
        'stats' => $stats
    ]);
}

/**
 * Check if user can rate event
 */
public function checkCanRate($eventId)
{
    if (!$this->session->get('logged_in')) {
        return $this->response->setJSON([
            'can_rate' => false,
            'reason' => 'Login required'
        ]);
    }

    $ratingModel = new \App\Models\RatingModel();
    $userId = $this->session->get('user_id');
    
    $result = $ratingModel->canUserRate($userId, $eventId);
    
    return $this->response->setJSON($result);
}

}

