<?php

namespace App\Config;

/**
 * Booking Status Constants
 * Standarisasi status booking sesuai database enum
 */
class BookingStatus
{
    // Status sesuai dengan database enum
    const PENDING = 'Pending';
    const WAITING_PAYMENT = 'Waiting Payment';
    const LUNAS = 'Lunas';  // Confirmed/Paid
    const DIBATALKAN = 'Dibatalkan';  // Cancelled
    const EXPIRED = 'Expired';
    const WAITING_APPROVAL = 'Waiting Approval'; // Setelah upload bukti
    
    /**
     * Get all status options
     */
    public static function getAll()
    {
        return [
            self::PENDING,
            self::WAITING_PAYMENT,
            self::WAITING_APPROVAL,
            self::LUNAS,
            self::DIBATALKAN,
            self::EXPIRED
        ];
    }
    
    /**
     * Get status badge color class
     */
    public static function getBadgeClass($status)
    {
        switch ($status) {
            case self::LUNAS:
                return 'status-confirmed';
            case self::PENDING:
                return 'status-pending';
            case self::WAITING_PAYMENT:
            case self::WAITING_APPROVAL:
                return 'status-waiting-approval';
            case self::DIBATALKAN:
            case self::EXPIRED:
                return 'status-cancelled';
            default:
                return 'status-pending';
        }
    }
    
    /**
     * Get status label for display
     */
    public static function getLabel($status)
    {
        $labels = [
            self::PENDING => 'Pending',
            self::WAITING_PAYMENT => 'Menunggu Pembayaran',
            self::WAITING_APPROVAL => 'Menunggu Verifikasi',
            self::LUNAS => 'Terkonfirmasi',
            self::DIBATALKAN => 'Dibatalkan',
            self::EXPIRED => 'Kadaluarsa'
        ];
        
        return $labels[$status] ?? $status;
    }
}