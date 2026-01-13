<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentProofModel extends Model
{
    protected $table = 'payment_proofs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'booking_id', 'booking_number', 'file_name', 
        'file_path', 'file_type', 'file_size'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'uploaded_at';
    protected $updatedField = false;
    
    /**
     * Get proof by booking number
     */
    public function getByBookingNumber($bookingNumber)
    {
        return $this->where('booking_number', $bookingNumber)->first();
    }
    
    /**
     * Get all proofs for a booking
     */
    public function getProofsByBooking($bookingId)
    {
        return $this->where('booking_id', $bookingId)
                    ->orderBy('uploaded_at', 'DESC')
                    ->findAll();
    }
}
