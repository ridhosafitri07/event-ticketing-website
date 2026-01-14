<?php

use App\Config\BookingStatus;

$title = $title ?? 'Detail User - EventKu';
$userStats = $userStats ?? [
    'total' => 0,
    'confirmed' => 0,
    'pending' => 0,
    'cancelled' => 0,
    'total_spent' => 0,
];

$extraStyles = <<<CSS
    .admin-container { max-width: 1200px; margin: 0 auto; }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
    }
    .page-header h1 { font-size: 22px; color: var(--gray-900); margin: 0; }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 900;
        text-decoration: none;
        border: 1px solid var(--gray-200);
        background: white;
        color: var(--gray-800);
        transition: all 0.2s ease;
    }
    .btn:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08); }

    .grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .card {
        background: white;
        border-radius: 14px;
        border: 1px solid var(--gray-200);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }
    .card-header {
        padding: 16px 18px;
        border-bottom: 1px solid var(--gray-100);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .card-title { font-size: 14px; font-weight: 900; color: var(--gray-900); margin: 0; }
    .card-body { padding: 18px; }

    .user-row {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 900;
        font-size: 16px;
        flex: 0 0 auto;
    }
    .user-meta h2 { margin: 0 0 4px 0; font-size: 16px; font-weight: 900; color: var(--gray-900); }
    .user-meta p { margin: 0; font-size: 13px; color: var(--gray-500); }

    .kv {
        display: grid;
        grid-template-columns: 160px 1fr;
        gap: 10px 14px;
        align-items: center;
    }
    .k { color: var(--gray-500); font-weight: 800; font-size: 13px; }
    .v { color: var(--gray-900); font-weight: 900; font-size: 13px; }

    .stats {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 12px;
    }
    .stat {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: 14px;
        padding: 12px;
    }
    .stat .label { font-size: 11px; color: var(--gray-500); font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat .value { margin-top: 6px; font-size: 16px; font-weight: 900; color: var(--gray-900); }

    .table-wrap { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; }
    .table th {
        background: var(--gray-50);
        padding: 14px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 900;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--gray-200);
        white-space: nowrap;
    }
    .table td { padding: 14px 16px; border-bottom: 1px solid var(--gray-100); font-size: 14px; vertical-align: top; }
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

    @media (max-width: 900px) {
        .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .kv { grid-template-columns: 1fr; }
    }
CSS;

$this->setVar('title', $title);
$this->setVar('activePage', 'users');
$this->setVar('extraStyles', $extraStyles);
?>

<?= $this->include('admin/_partials/layout_top') ?>

<div class="admin-container">
    <div class="page-header">
        <h1>👤 Detail User</h1>
        <a class="btn" href="<?= base_url('admin/users') ?>">← Kembali</a>
    </div>

    <div class="grid">
        <div class="card">
            <div class="card-header">
                <div class="user-row">
                    <div class="avatar"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></div>
                    <div class="user-meta">
                        <h2><?= esc($user['name'] ?? '-') ?></h2>
                        <p>User ID: #<?= esc((string)($user['id'] ?? '-')) ?></p>
                    </div>
                </div>
                <span class="badge"><?= esc($user['email'] ?? '-') ?></span>
            </div>
            <div class="card-body">
                <div class="kv">
                    <div class="k">Nomor HP</div>
                    <div class="v"><?= esc($user['phone'] ?? '-') ?></div>

                    <div class="k">Terdaftar Sejak</div>
                    <div class="v">
                        <?php if (!empty($user['registered_at'])): ?>
                            <?= date('d M Y H:i', strtotime($user['registered_at'])) ?> WIB
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📊 Statistik Booking</h3>
            </div>
            <div class="card-body">
                <div class="stats">
                    <div class="stat">
                        <div class="label">Total</div>
                        <div class="value"><?= (int)($userStats['total'] ?? 0) ?></div>
                    </div>
                    <div class="stat">
                        <div class="label">Terkonfirmasi</div>
                        <div class="value" style="color: var(--success);"><?= (int)($userStats['confirmed'] ?? 0) ?></div>
                    </div>
                    <div class="stat">
                        <div class="label">Pending</div>
                        <div class="value" style="color: var(--warning);"><?= (int)($userStats['pending'] ?? 0) ?></div>
                    </div>
                    <div class="stat">
                        <div class="label">Dibatalkan/Expired</div>
                        <div class="value" style="color: var(--danger);"><?= (int)($userStats['cancelled'] ?? 0) ?></div>
                    </div>
                    <div class="stat">
                        <div class="label">Total Pengeluaran</div>
                        <div class="value">Rp <?= number_format((int)($userStats['total_spent'] ?? 0), 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">🎫 Riwayat Booking</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($bookings)): ?>
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Booking</th>
                                    <th>Event</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $b):
                                    $status = $b['status'] ?? '';
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
                                            <div style="font-weight: 900; color: var(--gray-900);">#<?= esc($b['booking_number'] ?? '-') ?></div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 900; color: var(--gray-900);"><?= esc($b['event_title'] ?? '-') ?></div>
                                            <?php if (!empty($b['event_date'])): ?>
                                                <div style="font-size: 12px; color: var(--gray-500);">📅 <?= esc($b['event_date']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= (int)($b['ticket_count'] ?? 0) ?></td>
                                        <td>Rp <?= number_format((int)($b['total_price'] ?? 0), 0, ',', '.') ?></td>
                                        <td><span class="badge"><?= esc($b['payment_method'] ?? '-') ?></span></td>
                                        <td><span class="badge <?= $badgeClass ?>"><?= esc(BookingStatus::getLabel($status)) ?></span></td>
                                        <td>
                                            <?php if (!empty($b['booking_date'])): ?>
                                                <?= date('d M Y H:i', strtotime($b['booking_date'])) ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 30px 12px; color: var(--gray-500);">
                        Belum ada booking untuk user ini.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/_partials/layout_bottom') ?>
