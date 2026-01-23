<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentDateAndRejectReasonToPaymentProofs extends Migration
{
    public function up()
    {
        $fields = [
            'payment_date' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'file_size',
                'comment' => 'Tanggal dan waktu transfer manual'
            ],
            'reject_reason' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'payment_date',
                'comment' => 'Alasan penolakan dari admin'
            ]
        ];

        $this->forge->addColumn('payment_proofs', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('payment_proofs', ['payment_date', 'reject_reason']);
    }
}
