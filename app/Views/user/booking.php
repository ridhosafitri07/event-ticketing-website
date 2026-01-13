<?php
$this->setVar('showNavbar', true);
$this->setVar('bodyClass', 'dashboard-body');
?>
<?= $this->include('templates/header') ?>

<div class="container booking-page">
    <div class="booking-grid">
        <!-- Event Detail Card -->
        <div class="event-detail-card">
            <?php if (!empty($event['image'])): ?>
                <img src="<?= base_url($event['image']) ?>" alt="<?= esc($event['title']) ?>" class="event-detail-img">
            <?php else: ?>
                <div class="event-detail-img-placeholder">
                    <span style="font-size: 80px;"><?= esc($event['icon'] ?? '🎉') ?></span>
                </div>
            <?php endif; ?>
            
            <div class="event-detail-body">
                <span class="category-badge"><?= esc($event['category']) ?></span>
                <h1 class="event-detail-title"><?= esc($event['title']) ?></h1>
                
                <div class="event-detail-info">
                    <div class="info-row">
                        <span class="info-icon">📅</span>
                        <div>
                            <p class="info-label">Tanggal Event</p>
                            <p class="info-value"><?= date('d F Y', strtotime($event['date'])) ?></p>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-icon">📍</span>
                        <div>
                            <p class="info-label">Lokasi</p>
                            <p class="info-value"><?= esc($event['location']) ?></p>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-icon">🎫</span>
                        <div>
                            <p class="info-label">Tiket Tersedia</p>
                            <p class="info-value"><?= number_format($event['available_tickets']) ?> tiket</p>
                        </div>
                    </div>
                </div>

                <?php if (!empty($event['description'])): ?>
                    <div class="event-description">
                        <h3>Deskripsi Event</h3>
                        <p><?= nl2br(esc($event['description'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Booking Form Card -->
        <div class="booking-form-card">
            <h2 class="form-card-title">🎫 Booking Tiket</h2>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="error-box"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="error-box">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <p>✗ <?= esc($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('user/booking/process') ?>" method="POST" id="bookingForm">
                <?= csrf_field() ?>
                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">

                <div class="form-group">
                    <label>Jumlah Tiket</label>
                    <input 
                        type="number" 
                        id="ticket_count" 
                        name="ticket_count" 
                        min="1" 
                        max="<?= $event['available_tickets'] ?>" 
                        value="1" 
                        required
                        oninput="calculateTotal()"
                        class="form-input"
                    >
                    <small class="form-hint">Maksimal: <?= number_format($event['available_tickets']) ?> tiket</small>
                </div>

                <div class="price-summary">
                    <div class="price-row">
                        <span>Harga per tiket</span>
                        <span class="price-val">Rp <?= number_format($event['price'], 0, ',', '.') ?></span>
                    </div>
                    <div class="price-row">
                        <span>Jumlah tiket</span>
                        <span id="ticketQty">1</span>
                    </div>
                    <div class="price-divider"></div>
                    <div class="price-row price-total">
                        <span>Total Harga</span>
                        <span id="totalPrice">Rp <?= number_format($event['price'], 0, ',', '.') ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="Midtrans" required>
                            <div class="payment-card">
                                <span class="payment-icon">💳</span>
                                <div>
                                    <strong>Midtrans (Online)</strong>
                                    <p>Kartu Kredit, E-wallet, VA Bank</p>
                                </div>
                            </div>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="Transfer Manual" required checked>
                            <div class="payment-card">
                                <span class="payment-icon">🏦</span>
                                <div>
                                    <strong>Transfer Manual</strong>
                                    <p>Upload bukti transfer</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-book btn-large">
                        🎫 Booking Sekarang
                    </button>
                    <a href="<?= base_url('user/dashboard') ?>" class="btn-secondary btn-large">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const pricePerTicket = <?= $event['price'] ?>;

function calculateTotal() {
    const ticketCount = document.getElementById('ticket_count').value || 1;
    const total = pricePerTicket * ticketCount;
    
    document.getElementById('ticketQty').textContent = ticketCount;
    document.getElementById('totalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
}
</script>

<?= $this->include('templates/footer') ?>
