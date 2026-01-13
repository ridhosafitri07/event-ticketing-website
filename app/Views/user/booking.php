<?php
/**
 * View: Booking Event
 * Form booking tiket event
 */
$this->setVar('showNavbar', true);
$this->setVar('bodyClass', 'dashboard-body');
?>

<?= $this->include('templates/header') ?>

<div class="container">
    <div class="booking-container">
        <!-- Event Detail Card -->
        <div class="event-detail-card">
            <div class="event-detail-image">
                <?php if (!empty($event['image'])): ?>
                    <img src="<?= base_url($event['image']) ?>" alt="<?= esc($event['title']) ?>">
                <?php else: ?>
                    <div class="event-image-placeholder">
                        <span class="event-icon-large"><?= esc($event['icon'] ?? '🎉') ?></span>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="event-detail-content">
                <span class="event-category-badge"><?= esc($event['category']) ?></span>
                <h1 class="event-detail-title"><?= esc($event['title']) ?></h1>
                
                <div class="event-detail-meta">
                    <div class="meta-item-large">
                        <span class="meta-icon">📅</span>
                        <div>
                            <p class="meta-label">Tanggal Event</p>
                            <p class="meta-value"><?= date('d F Y', strtotime($event['date'])) ?></p>
                        </div>
                    </div>
                    
                    <div class="meta-item-large">
                        <span class="meta-icon">📍</span>
                        <div>
                            <p class="meta-label">Lokasi</p>
                            <p class="meta-value"><?= esc($event['location']) ?></p>
                        </div>
                    </div>
                    
                    <div class="meta-item-large">
                        <span class="meta-icon">🎫</span>
                        <div>
                            <p class="meta-label">Tiket Tersedia</p>
                            <p class="meta-value"><?= number_format($event['available_tickets']) ?> tiket</p>
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
            <h2>🎫 Booking Tiket</h2>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error">
                    ✗ <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-error">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <p>✗ <?= esc($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('user/booking/process') ?>" method="POST" id="bookingForm">
                <?= csrf_field() ?>
                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">

                <div class="form-group">
                    <label for="ticket_count">Jumlah Tiket</label>
                    <input 
                        type="number" 
                        id="ticket_count" 
                        name="ticket_count" 
                        min="1" 
                        max="<?= $event['available_tickets'] ?>" 
                        value="1" 
                        required
                        oninput="calculateTotal()"
                    >
                    <small>Maksimal: <?= number_format($event['available_tickets']) ?> tiket</small>
                </div>

                <div class="price-breakdown">
                    <div class="price-row">
                        <span>Harga per tiket</span>
                        <span id="pricePerTicket">Rp <?= number_format($event['price'], 0, ',', '.') ?></span>
                    </div>
                    <div class="price-row">
                        <span>Jumlah tiket</span>
                        <span id="ticketQty">1</span>
                    </div>
                    <hr>
                    <div class="price-row total">
                        <span>Total Harga</span>
                        <span id="totalPrice">Rp <?= number_format($event['price'], 0, ',', '.') ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <div class="payment-methods">
                        <label class="payment-method-option">
                            <input type="radio" name="payment_method" value="Midtrans" required>
                            <div class="payment-method-card">
                                <span class="payment-icon">💳</span>
                                <div>
                                    <strong>Midtrans (Online)</strong>
                                    <p>Kartu Kredit, E-wallet, VA Bank</p>
                                </div>
                            </div>
                        </label>

                        <label class="payment-method-option">
                            <input type="radio" name="payment_method" value="Transfer Manual" required checked>
                            <div class="payment-method-card">
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
                    <button type="submit" class="btn btn-primary btn-block btn-large">
                        🎫 Booking Sekarang
                    </button>
                    <a href="<?= base_url('user/dashboard') ?>" class="btn btn-secondary btn-block">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
const pricePerTicket = <?= $event['price'] ?>;

function calculateTotal() {
    const ticketCount = document.getElementById('ticket_count').value || 1;
    const total = pricePerTicket * ticketCount;
    
    document.getElementById('ticketQty').textContent = ticketCount;
    document.getElementById('totalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
<?= $this->endSection() ?>

<?= $this->include('templates/footer') ?>
