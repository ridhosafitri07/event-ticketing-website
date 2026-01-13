<?php

namespace App\Models;

use CodeIgniter\Model;

class MidtransTransactionModel extends Model
{
    protected $table = 'midtrans_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'booking_id', 'booking_number', 'order_id', 'transaction_id',
        'payment_type', 'gross_amount', 'transaction_status', 'fraud_status',
        'status_code', 'status_message', 'snap_token', 'snap_redirect_url',
        'bank', 'va_number', 'payment_code', 'pdf_url',
        'transaction_time', 'settlement_time', 'expiry_time', 'raw_response'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get transaction by order ID
     */
    public function getByOrderId($orderId)
    {
        return $this->where('order_id', $orderId)->first();
    }
    
    /**
     * Get transaction by booking number
     */
    public function getByBookingNumber($bookingNumber)
    {
        return $this->where('booking_number', $bookingNumber)->first();
    }
    
    /**
     * Update transaction status from notification
     */
    public function updateFromNotification($orderId, $notificationData)
    {
        $data = [
            'transaction_id' => $notificationData['transaction_id'] ?? null,
            'payment_type' => $notificationData['payment_type'] ?? null,
            'transaction_status' => $notificationData['transaction_status'] ?? null,
            'fraud_status' => $notificationData['fraud_status'] ?? null,
            'status_code' => $notificationData['status_code'] ?? null,
            'status_message' => $notificationData['status_message'] ?? null,
            'transaction_time' => $notificationData['transaction_time'] ?? null,
            'settlement_time' => $notificationData['settlement_time'] ?? null,
            'raw_response' => json_encode($notificationData)
        ];
        
        // Add bank info if exists
        if (isset($notificationData['bank'])) {
            $data['bank'] = $notificationData['bank'];
        }
        
        if (isset($notificationData['va_number'])) {
            $data['va_number'] = $notificationData['va_number'];
        }
        
        if (isset($notificationData['payment_code'])) {
            $data['payment_code'] = $notificationData['payment_code'];
        }
        
        return $this->where('order_id', $orderId)->set($data)->update();
    }
}
