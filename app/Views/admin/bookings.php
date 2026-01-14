<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Manage Bookings - EventKu') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --dark: #1e293b;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.6;
        }

        /* SIDEBAR - Same as dashboard */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 260px;
            background: white;
            border-right: 1px solid var(--gray-200);
            padding: 24px 0;
            overflow-y: auto;
            z-index: 100;
        }

        .sidebar-header {
            padding: 0 24px 24px;
            border-bottom: 1px solid var(--gray-200);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
        }

        .sidebar-logo svg {
            width: 32px;
            height: 32px;
        }

        .sidebar-nav {
            padding: 16px 0;
        }

        .nav-section {
            margin-bottom: 24px;
        }

        .nav-section-title {
            padding: 0 24px 8px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gray-500);
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 24px;
            color: var(--gray-700);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-link:hover {
            background: var(--gray-50);
            color: var(--primary);
        }

        .nav-link.active {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary-dark);
        }

        .nav-link svg {
            width: 20px;
            height: 20px;
        }

        .nav-badge {
            margin-left: auto;
            padding: 2px 8px;
            background: var(--danger);
            color: white;
            font-size: 11px;
            font-weight: 600;
            border-radius: 10px;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }

        .topbar {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .search-bar {
            position: relative;
            width: 400px;
        }

        .search-bar input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            font-size: 14px;
        }

        .search-bar svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--gray-400);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 12px 6px 6px;
            border-radius: 8px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
        }

        .user-role {
            font-size: 12px;
            color: var(--gray-500);
        }

        /* CONTENT */
        .content {
            padding: 32px;
        }

        .page-header {
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 8px;
        }

        .page-subtitle {
            color: var(--gray-600);
            font-size: 14px;
        }

        /* FILTERS */
        .filter-tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 10px 20px;
            background: white;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            text-decoration: none;
            color: var(--gray-600);
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }

        .filter-tab:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .filter-tab.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        /* TABLE */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--gray-50);
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: var(--gray-700);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 16px;
            border-top: 1px solid var(--gray-200);
            font-size: 14px;
        }

        tbody tr:hover {
            background: var(--gray-50);
        }

        /* STATUS BADGES */
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-lunas {
            background: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-waiting-approval {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-dibatalkan {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-expired {
            background: #e5e7eb;
            color: #374151;
        }

        .status-waiting-payment {
            background: #fce7f3;
            color: #831843;
        }

        /* BUTTONS */
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-secondary {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        /* MODAL */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
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
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            padding: 24px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            font-size: 20px;
            margin: 0;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--gray-500);
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group input[readonly] {
            background: var(--gray-50);
            cursor: not-allowed;
        }

        /* EMPTY STATE */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: var(--gray-500);
        }

        .empty-state h3 {
            margin-top: 16px;
            font-size: 18px;
            color: var(--gray-700);
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <svg fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 7h-4V4c0-1.1-.9-2-2-2h-4c-1.1 0-2 .9-2 2v3H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zM10 4h4v3h-4V4zm10 16H4V9h16v11z"/>
                </svg>
                <span>EventKu Admin</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Menu Utama</div>
                <a href="<?= base_url('admin/dashboard') ?>" class="nav-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="<?= base_url('admin/events') ?>" class="nav-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Events</span>
                </a>
                <a href="<?= base_url('admin/bookings') ?>" class="nav-link active">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Bookings</span>
                    <?php if (isset($stats['pending_payments']) && $stats['pending_payments'] > 0): ?>
                    <span class="nav-badge"><?= $stats['pending_payments'] ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Manajemen</div>
                <a href="<?= base_url('admin/users') ?>" class="nav-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Users</span>
                </a>
                <a href="<?= base_url('admin/payments') ?>" class="nav-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Payments</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Pengaturan</div>
                <a href="<?= base_url('admin/settings') ?>" class="nav-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Settings</span>
                </a>
                <a href="<?= base_url('admin/logout') ?>" class="nav-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- TOPBAR -->
        <div class="topbar">
            <div class="search-bar">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" placeholder="Search bookings...">
            </div>

            <div class="user-menu">
                <div class="user-avatar"><?= strtoupper(substr($admin['username'] ?? 'AD', 0, 2)) ?></div>
                <div>
                    <div class="user-name"><?= $admin['full_name'] ?? 'Admin' ?></div>
                    <div class="user-role"><?= ucwords(str_replace('_', ' ', $admin['role'] ?? 'admin')) ?></div>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="content">
            <div class="page-header">
                <h1 class="page-title">Manage Bookings</h1>
                <p class="page-subtitle">Kelola dan verifikasi semua booking event</p>
            </div>

            <!-- FILTER TABS -->
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
                <a href="<?= base_url('admin/bookings?status=Waiting Payment') ?>" class="filter-tab <?= $current_status === 'Waiting Payment' ? 'active' : '' ?>">
                    Waiting Payment
                </a>
                <a href="<?= base_url('admin/bookings?status=Dibatalkan') ?>" class="filter-tab <?= $current_status === 'Dibatalkan' ? 'active' : '' ?>">
                    Dibatalkan
                </a>
            </div>

            <!-- TABLE -->
            <div class="card">
                <?php if (!empty($bookings)): ?>
                    <table>
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
                                    <td><?= esc($booking['customer_name'] ?? 'User #' . $booking['user_id']) ?></td>
                                    <td><?= $booking['ticket_count'] ?> tiket</td>
                                    <td>Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php if ($booking['payment_method'] === 'manual_transfer'): ?>
                                            Transfer Manual
                                        <?php elseif ($booking['payment_method'] === 'midtrans'): ?>
                                            Midtrans
                                        <?php else: ?>
                                            <?= esc($booking['payment_method']) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $booking['status'])) ?>">
                                            <?= esc($booking['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($booking['booking_date'])) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($booking['status'] === 'Waiting Approval' && $booking['payment_method'] === 'manual_transfer'): ?>
                                                <a href="<?= base_url('admin/payment-proof/' . $booking['id']) ?>" class="btn btn-primary" title="Lihat Bukti Transfer">
                                                    👁️ Lihat Bukti
                                                </a>
                                                <form action="<?= base_url('admin/approve/' . $booking['id']) ?>" method="POST" style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-success" onclick="return confirm('Approve pembayaran untuk booking <?= esc($booking['booking_number']) ?>?')" title="Approve Pembayaran">
                                                        ✓ Approve
                                                    </button>
                                                </form>
                                                <button class="btn btn-danger" onclick="showRejectModal(<?= $booking['id'] ?>, '<?= esc($booking['booking_number']) ?>')" title="Reject Pembayaran">
                                                    ✗ Reject
                                                </button>
                                            <?php elseif ($booking['status'] === 'Waiting Payment' && $booking['payment_method'] === 'midtrans'): ?>
                                                <span class="btn btn-secondary" style="cursor: default;">Menunggu Bayar Midtrans</span>
                                            <?php else: ?>
                                                <span class="btn btn-secondary" style="cursor: default;">-</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <div style="font-size: 64px;">📋</div>
                        <h3>Tidak ada booking</h3>
                        <p>Belum ada booking untuk filter ini</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- REJECT MODAL -->
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
                    <input type="text" id="rejectBookingNumber" readonly style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;">
                </div>
                <div class="form-group">
                    <label>Alasan Penolakan *</label>
                    <textarea name="reject_reason" rows="4" placeholder="Contoh: Bukti transfer tidak valid, nominal tidak sesuai, rekening pengirim tidak sesuai..." required style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;"></textarea>
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
