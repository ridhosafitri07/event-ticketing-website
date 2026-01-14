<?php
$this->setVar('bodyClass', 'admin-body');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Manage Bookings - EventKu') ?></title>
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
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }
        .page-header h1 {
            font-size: 28px;
            color: #1e293b;
            margin: 0;
        }
        .filter-tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        .filter-tab {
            padding: 10px 20px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            text-decoration: none;
            color: #64748b;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }
        .filter-tab:hover {
            border-color: #667eea;
            color: #667eea;
        }
        .filter-tab.active {
            background: #667eea;
            border-color: #667eea;
            color: white;
        }
        .bookings-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th {
            background: #f8fafc;
            padding: 16px;
            text-align: left;
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table td {
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        .table tr:hover {
            background: #f8fafc;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status-waiting-approval {
            background: #dbeafe;
            color: #1e40af;
        }
        .status-confirmed {
            background: #d1fae5;
            color: #065f46;
        }
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .btn-success {
            background: #10b981;
            color: white;
        }
        .btn-success:hover {
            background: #059669;
        }
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        .btn-danger:hover {
            background: #dc2626;
        }
        .btn-secondary {
            background: #94a3b8;
            color: white;
        }
        .btn-secondary:hover {
            background: #64748b;
        }
        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-weight: 600;
            font-size: 14px;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: white;
            padding: 24px;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .modal-header h3 {
            margin: 0;
            color: #1e293b;
        }
        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #94a3b8;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #475569;
            font-size: 14px;
        }
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }
        .empty-state img {
            width: 200px;
            opacity: 0.5;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="admin-body">

<!-- Admin Navbar -->
<nav class="admin-navbar">
    <h2>🎫 EventKu Admin</h2>
    <div class="admin-nav-links">
        <a href="<?= base_url('admin/dashboard') ?>">📊 Dashboard</a>
        <a href="<?= base_url('admin/bookings') ?>" class="active">🎫 Bookings</a>
        <a href="<?= base_url('admin/events') ?>">🎉 Events</a>
        <a href="<?= base_url('admin/users') ?>">👥 Users</a>
        <span style="color: #94a3b8;">|</span>
        <span style="color: #cbd5e1;"><?= esc($admin['full_name'] ?? 'Admin') ?></span>
        <a href="<?= base_url('admin/logout') ?>" style="color: #f87171;">Logout</a>
    </div>
</nav>

<div class="admin-container">
    <div class="page-header">
        <h1>📋 Manage Bookings</h1>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">✓ <?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">✗ <?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <a href="<?= base_url('admin/bookings') ?>" class="filter-tab <?= empty($current_status) ? 'active' : '' ?>">
            Semua Booking
        </a>
        <a href="<?= base_url('admin/bookings?status=Waiting Approval') ?>" class="filter-tab <?= $current_status === 'Waiting Approval' ? 'active' : '' ?>">
            Menunggu Verifikasi
        </a>
        <a href="<?= base_url('admin/bookings?status=Lunas') ?>" class="filter-tab <?= $current_status === 'Lunas' ? 'active' : '' ?>">
            Terkonfirmasi
        </a>
        <a href="<?= base_url('admin/bookings?status=Pending') ?>" class="filter-tab <?= $current_status === 'Pending' ? 'active' : '' ?>">
            Pending
        </a>
        <a href="<?= base_url('admin/bookings?status=Dibatalkan') ?>" class="filter-tab <?= $current_status === 'Dibatalkan' ? 'active' : '' ?>">
            Dibatalkan
        </a>
    </div>

    <!-- Bookings Table -->
    <div class="bookings-table">
        <?php if (!empty($bookings)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Booking #</th>
                        <th>Event</th>
                        <th>Customer</th>
                        <th>Tiket</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><strong><?= esc($booking['booking_number']) ?></strong></td>
                            <td><?= esc($booking['event_title']) ?></td>
                            <td>User #<?= $booking['user_id'] ?></td>
                            <td><?= $booking['ticket_count'] ?> tiket</td>
                            <td>Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></td>
                            <td><?= esc($booking['payment_method']) ?></td>
                            <td>
                                <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $booking['status'])) ?>">
                                    <?= esc($booking['status']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($booking['booking_date'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($booking['status'] === 'Waiting Approval'): ?>
                                        <a href="<?= base_url('admin/payment-proof/' . $booking['id']) ?>" class="btn btn-primary">
                                            👁️ Lihat Bukti
                                        </a>
                                        <form action="<?= base_url('admin/approve/' . $booking['id']) ?>" method="POST" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-success" onclick="return confirm('Approve pembayaran ini?')">
                                                ✓ Approve
                                            </button>
                                        </form>
                                        <button class="btn btn-danger" onclick="showRejectModal(<?= $booking['id'] ?>, '<?= esc($booking['booking_number']) ?>')">
                                            ✗ Reject
                                        </button>
                                    <?php else: ?>
                                        <span class="btn btn-secondary" style="cursor: not-allowed;">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <div style="font-size: 64px; margin-bottom: 16px;">📋</div>
                <h3>Tidak ada booking</h3>
                <p>Belum ada booking untuk filter ini</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal" id="rejectModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Reject Pembayaran</h3>
            <button class="close-btn" onclick="closeRejectModal()">&times;</button>
        </div>
        <form id="rejectForm" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Booking Number</label>
                <input type="text" id="rejectBookingNumber" readonly style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc;">
            </div>
            <div class="form-group">
                <label>Alasan Penolakan *</label>
                <textarea name="reject_reason" rows="4" placeholder="Contoh: Bukti transfer tidak valid, nominal tidak sesuai..." required></textarea>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Batal</button>
                <button type="submit" class="btn btn-danger">Reject Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal(bookingId, bookingNumber) {
    document.getElementById('rejectForm').action = '<?= base_url('admin/reject/') ?>' + bookingId;
    document.getElementById('rejectBookingNumber').value = bookingNumber;
    document.getElementById('rejectModal').classList.add('active');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('active');
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>

</body>
</html>