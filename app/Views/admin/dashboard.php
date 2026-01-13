<?php
/**
 * View: Admin Dashboard
 * Main admin panel dengan statistik
 */
$this->setVar('bodyClass', 'admin-body');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin Dashboard - EventKu') ?></title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <style>
        .admin-body {
            background: #f1f5f9;
            min-height: 100vh;
        }
        .admin-navbar {
            background: #1e293b;
            color: white;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .admin-navbar h2 {
            margin: 0;
            font-size: 20px;
        }
        .admin-nav-links {
            display: flex;
            gap: 20px;
        }
        .admin-nav-links a {
            color: #cbd5e1;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .admin-nav-links a:hover, .admin-nav-links a.active {
            background: #334155;
            color: white;
        }
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px 24px;
        }
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .stat-card .stat-icon {
            font-size: 32px;
            margin-bottom: 12px;
        }
        .stat-card .stat-label {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
        }
        .recent-bookings {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .recent-bookings h3 {
            margin-bottom: 20px;
            color: #1e293b;
        }
        .booking-table {
            width: 100%;
            border-collapse: collapse;
        }
        .booking-table th {
            background: #f1f5f9;
            padding: 12px;
            text-align: left;
            font-size: 14px;
            color: #475569;
        }
        .booking-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status-confirmed {
            background: #d1fae5;
            color: #065f46;
        }
        .status-waiting-approval {
            background: #dbeafe;
            color: #1e40af;
        }
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body class="admin-body">

<!-- Admin Navbar -->
<nav class="admin-navbar">
    <h2>🎫 EventKu Admin</h2>
    <div class="admin-nav-links">
        <a href="<?= base_url('admin/dashboard') ?>" class="active">📊 Dashboard</a>
        <a href="<?= base_url('admin/bookings') ?>">🎫 Bookings</a>
        <a href="<?= base_url('admin/events') ?>">🎉 Events</a>
        <a href="<?= base_url('admin/users') ?>">👥 Users</a>
        <span style="color: #94a3b8;">|</span>
        <span style="color: #cbd5e1;"><?= esc($admin['full_name'] ?? 'Admin') ?></span>
        <a href="<?= base_url('admin/logout') ?>" style="color: #f87171;">Logout</a>
    </div>
</nav>

<div class="admin-container">
    <h1 style="margin-bottom: 24px; color: #1e293b;">Dashboard Overview</h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" style="margin-bottom: 24px;">
            ✓ <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon">🎫</div>
            <div class="stat-label">Total Bookings</div>
            <div class="stat-value"><?= number_format($stats['total_bookings'] ?? 0) ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-label">Pending Payments</div>
            <div class="stat-value"><?= number_format($stats['pending_payments'] ?? 0) ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-label">Confirmed Bookings</div>
            <div class="stat-value"><?= number_format($stats['confirmed_bookings'] ?? 0) ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">Rp <?= number_format($stats['total_revenue'] ?? 0, 0, ',', '.') ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-label">Total Users</div>
            <div class="stat-value"><?= number_format($stats['total_users'] ?? 0) ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">🎉</div>
            <div class="stat-label">Total Events</div>
            <div class="stat-value"><?= number_format($stats['total_events'] ?? 0) ?></div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="recent-bookings">
        <h3>📋 Recent Bookings</h3>
        
        <?php if (!empty($recentBookings)): ?>
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>Booking #</th>
                        <th>Event</th>
                        <th>User</th>
                        <th>Tiket</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentBookings as $booking): ?>
                        <tr>
                            <td><strong><?= esc($booking['booking_number']) ?></strong></td>
                            <td><?= esc($booking['event_title']) ?></td>
                            <td>User #<?= $booking['user_id'] ?></td>
                            <td><?= $booking['ticket_count'] ?> tiket</td>
                            <td>Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></td>
                            <td>
                                <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $booking['status'])) ?>">
                                    <?= esc($booking['status']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($booking['booking_date'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: #64748b; text-align: center; padding: 40px 0;">Belum ada booking</p>
        <?php endif; ?>
    </div>
</div>

<script src="<?= base_url('js/script.js') ?>"></script>
</body>
</html>
