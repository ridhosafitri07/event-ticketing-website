<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\BookingModel;
use App\Models\EventModel;
use App\Config\BookingStatus;

class CancelExpiredBookings extends BaseCommand
{
    protected $group = 'Booking';
    protected $name = 'booking:cancel-expired';
    protected $description = 'Cancel bookings yang melewati payment deadline';

    public function run(array $params)
    {
        $bookingModel = new BookingModel();
        $eventModel = new EventModel();
        
        // Cari booking yang sudah expired dan belum dibatalkan
        $expiredBookings = $bookingModel
            ->where('payment_deadline <', date('Y-m-d H:i:s'))
            ->whereIn('status', [BookingStatus::PENDING, BookingStatus::WAITING_PAYMENT])
            ->findAll();
        
        if (empty($expiredBookings)) {
            CLI::write('Tidak ada booking yang expired.', 'green');
            return;
        }
        
        $canceledCount = 0;
        $db = \Config\Database::connect();
        
        foreach ($expiredBookings as $booking) {
            $db->transStart();
            
            try {
                // Update booking status
                $bookingModel->update($booking['id'], [
                    'status' => BookingStatus::DIBATALKAN,
                    'payment_details' => json_encode([
                        'type' => 'auto_canceled_expired',
                        'reason' => 'Booking dibatalkan otomatis karena melewati batas waktu pembayaran',
                        'at' => date('Y-m-d H:i:s'),
                    ], JSON_UNESCAPED_UNICODE),
                    'cancelled_at' => date('Y-m-d H:i:s')
                ]);
                
                // Kembalikan tiket ke available
                $event = $eventModel->find($booking['event_id']);
                if ($event) {
                    $newAvailableTickets = $event['available_tickets'] + $booking['ticket_count'];
                    $eventModel->update($booking['event_id'], [
                        'available_tickets' => $newAvailableTickets
                    ]);
                }
                
                $db->transComplete();
                
                if ($db->transStatus() !== false) {
                    $canceledCount++;
                    CLI::write("✓ Booking #{$booking['booking_number']} dibatalkan (expired)", 'yellow');
                } else {
                    CLI::write("✗ Gagal membatalkan Booking #{$booking['booking_number']}", 'red');
                }
            } catch (\Exception $e) {
                $db->transRollback();
                CLI::write("✗ Error: " . $e->getMessage(), 'red');
            }
        }
        
        CLI::write("\nTotal booking yang dibatalkan: {$canceledCount}", 'green');
    }
}
