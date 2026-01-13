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
            <a href="<?= base_url('user/dashboard') ?>" class="btn-book btn-large" style="max-width: 300px; margin: 20px auto 0;">🎉 Lihat Event</a>
        </div>
    <?php endif; ?>
</div>

<?= $this->include('templates/footer') ?>
