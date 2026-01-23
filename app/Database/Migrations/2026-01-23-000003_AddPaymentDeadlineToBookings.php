<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentDeadlineToBookings extends Migration
{
    public function up()
    {
        $fields = [
            'payment_deadline' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'payment_confirmed_at',
                'comment' => 'Batas waktu upload bukti pembayaran (deadline)'
            ]
        ];

        $this->forge->addColumn('bookings', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('bookings', 'payment_deadline');
    }
}
