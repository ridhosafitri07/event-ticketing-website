<?php

use App\Config\BookingStatus;

$title = $title ?? 'Manage Payments - EventKu';
$current_status = $current_status ?? '';
$current_method = $current_method ?? '';
$stats = $stats ?? [];

$extraStyles = <<<CSS
    .admin-container { max-width: 1400px; margin: 0 auto; }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
    }
    .page-header h1 { font-size: 22px; color: var(--gray-900); margin: 0; }

    .cards {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }
    .card {
        background: white;
        border-radius: 14px;
        border: 1px solid var(--gray-200);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
        padding: 14px 16px;
    }
    .card .label { font-size: 12px; color: var(--gray-500); font-weight: 900; text-transform: uppercase; letter-spacing: .5px; }
    .card .value { margin-top: 8px; font-size: 18px; font-weight: 900; color: var(--gray-900); }

    .filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 14px;
    }
    .pill {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 999px;
        border: 1px solid var(--gray-200);
        background: white;
        text-decoration: none;
        color: var(--gray-800);
        font-weight: 900;
        font-size: 13px;
        transition: all 0.2s ease;
    }
    .pill:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08); }
    .pill.active { background: linear-gradient(to right, var(--primary), var(--secondary)); color: white; border-color: transparent; }

    .table-wrap {
        background: white;
        border-radius: 14px;
        border: 1px solid var(--gray-200);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }
    .table { width: 100%; border-collapse: collapse; }
    .table th {
        background: var(--gray-50);
        padding: 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 900;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--gray-200);
        white-space: nowrap;
    }
    .table td { padding: 16px; border-bottom: 1px solid var(--gray-100); font-size: 14px; vertical-align: top; }
    .table tr:hover { background: var(--gray-50); }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        border: 1px solid var(--gray-200);
        background: white;
        color: var(--gray-800);
        white-space: nowrap;
    }
    .badge-success { background: rgba(16, 185, 129, 0.10); border-color: rgba(16, 185, 129, 0.25); color: #0f766e; }
    .badge-warning { background: rgba(245, 158, 11, 0.10); border-color: rgba(245, 158, 11, 0.25); color: #b45309; }
    .badge-danger { background: rgba(239, 68, 68, 0.10); border-color: rgba(239, 68, 68, 0.25); color: #b91c1c; }

    .actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .btn {
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 900;
        border: 1px solid var(--gray-200);
        background: white;
        cursor: pointer;
        text-decoration: none;
        color: var(--gray-800);
        transition: all 0.2s ease;
    }
    .btn:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08); }
    .btn-primary { background: var(--primary); border-color: transparent; color: white; }
    .btn-primary:hover { background: var(--primary-dark); }
    .btn-danger { background: var(--danger); border-color: transparent; color: white; }

    .inline-form { display: inline; }

    @media (max-width: 1100px) {
        .cards { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
CSS;

$this->setVar('title', $title);
$this->setVar('activePage', 'payments');
$this->setVar('extraStyles', $extraStyles);
?>

<?= $this->include('admin/_partials/layout_top') ?>

<div class="admin-container">
    <div class="page-header">
        <h1>💳 Payments</h1>
    </div>

    <div class="cards">
        <div class="card">
            <div class="label">Waiting Approval</div>
            <div class="value"><?= (int)($stats['pending_payments'] ?? 0) ?></div>
        </div>
        <div class="card">
            <div class="label">Waiting Payment</div>
            <div class="value"><?= (int)($stats['waiting_payment'] ?? 0) ?></div>
        </div>
        <div class="card">
            <div class="label">Paid</div>
            <div class="value"><?= (int)($stats['paid'] ?? 0) ?></div>
        </div>
        <div class="card">
            <div class="label">Total Revenue</div>
            <div class="value">Rp <?= number_format((int)($stats['total_revenue'] ?? 0), 0, ',', '.') ?></div>
        </div>
    </div>

    <div class="filters">
        <?php
            $base = base_url('admin/payments');
            $methodQuery = $current_method ? ('&method=' . urlencode($current_method)) : '';
        ?>
        <a class="pill <?= $current_status === '' ? 'active' : '' ?>" href="<?= $base ?>">Semua</a>
        <a class="pill <?= $current_status === BookingStatus::WAITING_APPROVAL ? 'active' : '' ?>" href="<?= $base . '?status=' . urlencode(BookingStatus::WAITING_APPROVAL) . $methodQuery ?>">Waiting Approval</a>
        <a class="pill <?= $current_status === BookingStatus::WAITING_PAYMENT ? 'active' : '' ?>" href="<?= $base . '?status=' . urlencode(BookingStatus::WAITING_PAYMENT) . $methodQuery ?>">Waiting Payment</a>
        <a class="pill <?= $current_status === BookingStatus::LUNAS ? 'active' : '' ?>" href="<?= $base . '?status=' . urlencode(BookingStatus::LUNAS) . $methodQuery ?>">Paid</a>
        <a class="pill <?= $current_status === BookingStatus::DIBATALKAN ? 'active' : '' ?>" href="<?= $base . '?status=' . urlencode(BookingStatus::DIBATALKAN) . $methodQuery ?>">Cancelled</a>

        <?php
            $statusQuery = $current_status ? ('status=' . urlencode($current_status) . '&') : '';
        ?>
        <a class="pill <?= $current_method === '' ? 'active' : '' ?>" href="<?= $base . ($current_status ? ('?status=' . urlencode($current_status)) : '') ?>">All Methods</a>
        <a class="pill <?= $current_method === 'manual_transfer' ? 'active' : '' ?>" href="<?= $base . '?' . $statusQuery . 'method=manual_transfer' ?>">Manual Transfer</a>
        <a class="pill <?= $current_method === 'midtrans' ? 'active' : '' ?>" href="<?= $base . '?' . $statusQuery . 'method=midtrans' ?>">Midtrans</a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Booking</th>
                    <th>Customer</th>
                    <th>Event</th>
                    <th>Total</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($payments)): ?>
                    <?php foreach ($payments as $p):
                        $status = $p['status'] ?? '';
                        $badgeClass = '';
                        if ($status === BookingStatus::LUNAS) {
                            $badgeClass = 'badge-success';
                        } elseif (in_array($status, [BookingStatus::PENDING, BookingStatus::WAITING_PAYMENT, BookingStatus::WAITING_APPROVAL], true)) {
                            $badgeClass = 'badge-warning';
                        } elseif (in_array($status, [BookingStatus::DIBATALKAN, BookingStatus::EXPIRED], true)) {
                            $badgeClass = 'badge-danger';
                        }
                    ?>
                        <tr>
                            <td>
                                <div style="font-weight: 900;">#<?= esc($p['booking_number'] ?? '-') ?></div>
                            </td>
                            <td><?= esc($p['customer_name'] ?? '-') ?></td>
                            <td><?= esc($p['event_title'] ?? '-') ?></td>
                            <td>Rp <?= number_format((int)($p['total_price'] ?? 0), 0, ',', '.') ?></td>
                            <td><span class="badge"><?= esc($p['payment_method'] ?? '-') ?></span></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= esc(BookingStatus::getLabel($status)) ?></span></td>
                            <td>
                                <?php if (!empty($p['booking_date'])): ?>
                                    <?= date('d M Y H:i', strtotime($p['booking_date'])) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="actions">
                                    <?php if (($p['payment_method'] ?? '') === 'manual_transfer' && $status === BookingStatus::WAITING_APPROVAL): ?>
                                        <a class="btn" href="<?= base_url('admin/payment-proof/' . $p['id']) ?>">Lihat Bukti</a>

                                        <form class="inline-form" action="<?= base_url('admin/approve/' . $p['id']) ?>" method="post" onsubmit="return confirm('Approve pembayaran booking ini?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-primary">Approve</button>
                                        </form>

                                        <form class="inline-form" action="<?= base_url('admin/reject/' . $p['id']) ?>" method="post" onsubmit="return confirm('Reject pembayaran booking ini?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="reject_reason" value="Bukti transfer tidak valid">
                                            <button type="submit" class="btn btn-danger">Reject</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: var(--gray-500); font-weight: 800;">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding: 28px; color: var(--gray-500); font-weight: 800;">
                            Tidak ada data payment untuk filter ini.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->include('admin/_partials/layout_bottom') ?>
