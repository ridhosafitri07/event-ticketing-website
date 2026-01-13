<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'booking_number', 'user_id', 'event_id',
        'event_title', 'event_date', 'event_location', 'event_icon',
        'ticket_count', 'price_per_ticket', 'total_price',
        'payment_method', 'payment_channel', 'payment_details',
        'status', 'payment_confirmed_at', 'cancelled_at', 'expired_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'booking_date';
    protected $updatedField = 'updated_at';
    
    /**
     * Get user bookings
     */
    public function getUserBookings($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('booking_date', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get pending payments (for admin)
     */
    public function getPendingPayments()
    {
        return $this->whereIn('status', ['Pending', 'Waiting Payment'])
                    ->orderBy('booking_date', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get booking by booking number
     */
    public function getByBookingNumber($bookingNumber)
    {
        return $this->where('booking_number', $bookingNumber)->first();
    }
    
    /**
     * Generate unique booking number
     */
    public function generateBookingNumber()
    {
        do {
            $bookingNumber = 'BK' . date('Ymd') . rand(1000, 9999);
        } while ($this->where('booking_number', $bookingNumber)->first());
        
        return $bookingNumber;
    }
    
    /**
     * Update booking status
     */
    public function updateStatus($bookingId, $status, $additionalData = [])
    {
        $data = ['status' => $status];
        
        if ($status === 'Lunas') {
            $data['payment_confirmed_at'] = date('Y-m-d H:i:s');
        } elseif ($status === 'Dibatalkan') {
            $data['cancelled_at'] = date('Y-m-d H:i:s');
        } elseif ($status === 'Expired') {
            $data['expired_at'] = date('Y-m-d H:i:s');
        }
        
        $data = array_merge($data, $additionalData);
        
        return $this->update($bookingId, $data);
    }
}
