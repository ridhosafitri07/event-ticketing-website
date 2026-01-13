<?php
$this->setVar('showNavbar', true);
$this->setVar('bodyClass', 'dashboard-body');
?>
<?= $this->include('templates/header') ?>

<div class="container">
    <div class="dashboard-header">
        <h1>📋 Riwayat Booking</h1>
        <p>Lihat semua transaksi dan status booking kamu</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="success-box"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="error-box"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (!empty($bookings) && is_array($bookings)): ?>
        <div class="bookings-container">
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card-modern">
                    <div class="booking-card-header">
                        <div class="booking-event-info">
                            <span class="event-icon-badge"><?= esc($booking['event_icon'] ?? '🎉') ?></span>
                            <div class="booking-event-details">
                                <h3><?= esc($booking['event_title']) ?></h3>
                                <p class="booking-number">Booking #<?= esc($booking['booking_number']) ?></p>
                            </div>
                        </div>
                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $booking['status'])) ?>">
                            <?= esc($booking['status']) ?>
                        </span>
                    </div>
                    
                    <div class="booking-details-grid">
                        <div class="detail-item">
                            <span class="detail-icon">📅</span>
                            <div>
                                <p class="detail-label">Tanggal Event</p>
                                <p class="detail-value"><?= date('d M Y', strtotime($booking['event_date'])) ?></p>
                            </div>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon">📍</span>
                            <div>
                                <p class="detail-label">Lokasi</p>
                                <p class="detail-value"><?= esc($booking['event_location']) ?></p>
                            </div>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon">🎫</span>
                            <div>
                                <p class="detail-label">Jumlah Tiket</p>
                                <p class="detail-value"><?= number_format($booking['ticket_count']) ?> tiket</p>
                            </div>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon">💰</span>
                            <div>
                                <p class="detail-label">Total Harga</p>
                                <p class="detail-value" style="color: #667eea; font-weight: 700;">Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></p>
                            </div>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon">💳</span>
                            <div>
                                <p class="detail-label">Metode Pembayaran</p>
                                <p class="detail-value"><?= esc($booking['payment_method']) ?></p>
                            </div>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon">📅</span>
                            <div>
                                <p class="detail-label">Tanggal Booking</p>
                                <p class="detail-value"><?= date('d M Y H:i', strtotime($booking['booking_date'])) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="booking-actions-modern">
                        <?php if ($booking['status'] === 'Pending' && $booking['payment_method'] === 'Transfer Manual'): ?>
                            <form action="<?= base_url('payment/upload/' . $booking['id']) ?>" method="POST" enctype="multipart/form-data" style="display: inline-block;">
                                <?= csrf_field() ?>
                                <label for="payment_proof_<?= $booking['id'] ?>" class="btn-modern btn-upload">
                                    📤 Upload Bukti Transfer
                                </label>
                                <input type="file" id="payment_proof_<?= $booking['id'] ?>" name="payment_proof" accept="image/*,.pdf" style="display:none" onchange="this.form.submit()">
                            </form>
                        <?php endif; ?>
                        
                        <?php if (in_array($booking['status'], ['Pending', 'Waiting Payment'])): ?>
                            <a href="<?= base_url('user/cancelBooking/' . $booking['id']) ?>" class="btn-modern btn-cancel" onclick="return confirm('Yakin ingin membatalkan booking ini?')">
                                ❌ Cancel
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($booking['status'] === 'Confirmed'): ?>
                            <span class="confirmed-badge">✅ Terkonfirmasi</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">📋</div>
            <h3>Belum ada riwayat booking</h3>
            <p>Yuk booking event favoritmu sekarang!</p>
            <a href="<?= base_url('user/dashboard') ?>" class="btn-primary btn-large">🎉 Lihat Event</a>
        </div>
    <?php endif; ?>
</div>

<style>
.bookings-container {
    display: grid;
    gap: 20px;
    margin-top: 24px;
}
.booking-card-modern {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.booking-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e2e8f0;
}
.booking-event-info {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}
.event-icon-badge {
    font-size: 32px;
}
.booking-event-details h3 {
    margin: 0 0 4px 0;
    font-size: 18px;
    color: #1a202c;
}
.booking-number {
    color: #64748b;
    font-size: 13px;
    margin: 0;
}
.status-badge {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
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
.booking-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}
.detail-item {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}
.detail-icon {
    font-size: 20px;
}
.detail-label {
    font-size: 12px;
    color: #64748b;
    margin: 0 0 4px 0;
}
.detail-value {
    font-size: 14px;
    color: #1a202c;
    font-weight: 500;
    margin: 0;
}
.booking-actions-modern {
    display: flex;
    gap: 12px;
    align-items: center;
    padding-top: 16px;
    border-top: 1px solid #e2e8f0;
}
.btn-modern {
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-upload {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
}
.btn-upload:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}
.btn-cancel {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}
.btn-cancel:hover {
    background: #fecaca;
}
.confirmed-badge {
    padding: 8px 16px;
    background: #d1fae5;
    color: #065f46;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
}
</style>

<?= $this->include('templates/footer') ?>
