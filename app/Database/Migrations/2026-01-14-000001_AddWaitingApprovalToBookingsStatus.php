<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWaitingApprovalToBookingsStatus extends Migration
{
    public function up()
    {
        // MySQL ENUM needs full redefinition
        $this->db->query("ALTER TABLE `bookings` MODIFY `status` ENUM('Pending','Waiting Payment','Waiting Approval','Lunas','Dibatalkan','Expired') DEFAULT 'Pending'");
    }

    public function down()
    {
        // Revert to original enum (without Waiting Approval)
        $this->db->query("ALTER TABLE `bookings` MODIFY `status` ENUM('Pending','Waiting Payment','Lunas','Dibatalkan','Expired') DEFAULT 'Pending'");
    }
}
