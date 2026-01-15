<?php
$this->setVar('showNavbar', true);
$this->setVar('bodyClass', 'dashboard-body');
?>
<?= $this->include('templates/header') ?>

<div class="container">
    <!-- Header with Stats -->
    <div class="riwayat-header">
        <div class="riwayat-title-section">
            <h1>📋 Riwayat Booking</h1>
            <p>Lihat semua transaksi dan status booking kamu</p>
        </div>
        
        <!-- Quick Stats -->
        <div class="quick-stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🎫</div>
                <div class="stat-info">
                    <div class="stat-label">Total Booking</div>
                    <div class="stat-value"><?= count($bookings ?? []) ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-info">
                    <div class="stat-label">Terkonfirmasi</div>
                    <div class="stat-value">
                        <?php 
                        $confirmed = array_filter($bookings ?? [], fn($b) => $b['status'] === 'Lunas');
                        echo count($confirmed);
                        ?>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-info">
                    <div class="stat-label">Pending</div>
                    <div class="stat-value">
                        <?php 
                        $pending = array_filter($bookings ?? [], fn($b) => in_array($b['status'], ['Pending', 'Waiting Payment', 'Waiting Approval']));
                        echo count($pending);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="toast-notification success show">
            <div class="toast-icon">✓</div>
            <div class="toast-content">
                <div class="toast-title">Berhasil!</div>
                <div class="toast-message"><?= session()->getFlashdata('success') ?></div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">×</button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="toast-notification error show">
            <div class="toast-icon">✗</div>
            <div class="toast-content">
                <div class="toast-title">Error!</div>
                <div class="toast-message"><?= session()->getFlashdata('error') ?></div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">×</button>
        </div>
    <?php endif; ?>

    <!-- Filter Section -->
    <div class="booking-filter-section">
        <div class="filter-tabs-enhanced">
            <button class="filter-tab-enhanced active" onclick="filterBookings(event, 'all')">
                <span class="tab-icon">🔍</span>
                <span class="tab-label">Semua</span>
                <span class="tab-badge"><?= count($bookings ?? []) ?></span>
            </button>
            <button class="filter-tab-enhanced" onclick="filterBookings(event, 'pending')">
                <span class="tab-icon">⏳</span>
                <span class="tab-label">Pending</span>
                <span class="tab-badge">
                    <?php 
                    $pending = array_filter($bookings ?? [], fn($b) => $b['status'] === 'Pending');
                    echo count($pending);
                    ?>
                </span>
            </button>
            <button class="filter-tab-enhanced" onclick="filterBookings(event, 'waiting')">
                <span class="tab-icon">💳</span>
                <span class="tab-label">Menunggu</span>
                <span class="tab-badge">
                    <?php 
                    $waiting = array_filter($bookings ?? [], fn($b) => in_array($b['status'], ['Waiting Payment', 'Waiting Approval']));
                    echo count($waiting);
                    ?>
                </span>
            </button>
            <button class="filter-tab-enhanced" onclick="filterBookings(event, 'confirmed')">
                <span class="tab-icon">✅</span>
                <span class="tab-label">Lunas</span>
                <span class="tab-badge">
                    <?php 
                    $confirmed = array_filter($bookings ?? [], fn($b) => $b['status'] === 'Lunas');
                    echo count($confirmed);
                    ?>
                </span>
            </button>
            <button class="filter-tab-enhanced" onclick="filterBookings(event, 'cancelled')">
                <span class="tab-icon">❌</span>
                <span class="tab-label">Dibatalkan</span>
                <span class="tab-badge">
                    <?php 
                    $cancelled = array_filter($bookings ?? [], fn($b) => in_array($b['status'], ['Dibatalkan', 'Expired']));
                    echo count($cancelled);
                    ?>
                </span>
            </button>
        </div>
    </div>

    <?php if (!empty($bookings) && is_array($bookings)): ?>
        <div class="bookings-container-enhanced">
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card-premium" 
                     data-status="<?= strtolower($booking['status']) ?>"
                     data-filter-category="<?php 
                        if ($booking['status'] === 'Pending') echo 'pending';
                        elseif (in_array($booking['status'], ['Waiting Payment', 'Waiting Approval'])) echo 'waiting';
                        elseif ($booking['status'] === 'Lunas') echo 'confirmed';
                        else echo 'cancelled';
                     ?>">
                    
                    <!-- Card Header with Event Image -->
                    <div class="booking-card-visual">
                        <div class="booking-event-thumbnail">
                            <?php if (!empty($booking['event_icon'])): ?>
                                <div class="event-icon-large"><?= esc($booking['event_icon']) ?></div>
                            <?php else: ?>
                                <div class="event-icon-large">🎉</div>
                            <?php endif; ?>
                        </div>
                        <div class="booking-event-meta">
                            <div class="booking-number-badge">
                                <span class="badge-icon">🎫</span>
                                <span class="badge-text">#<?= esc($booking['booking_number']) ?></span>
                            </div>
                            <h3 class="booking-event-title"><?= esc($booking['event_title']) ?></h3>
                            <div class="booking-date-info">
                                <span class="date-icon">📅</span>
                                <span><?= date('d M Y', strtotime($booking['event_date'])) ?></span>
                                <span class="separator">•</span>
                                <span class="location-icon">📍</span>
                                <span><?= esc($booking['event_location']) ?></span>
                            </div>
                        </div>
                        <div class="booking-status-section">
                            <?php
                            $statusClass = match($booking['status']) {
                                'Pending' => 'status-pending',
                                'Waiting Payment', 'Waiting Approval' => 'status-waiting',
                                'Lunas' => 'status-confirmed',
                                'Dibatalkan', 'Expired' => 'status-cancelled',
                                default => 'status-pending'
                            };
                            
                            $statusLabel = match($booking['status']) {
                                'Pending' => 'Pending',
                                'Waiting Payment' => 'Menunggu Pembayaran',
                                'Waiting Approval' => 'Menunggu Verifikasi',
                                'Lunas' => 'Terkonfirmasi',
                                'Dibatalkan' => 'Dibatalkan',
                                'Expired' => 'Kadaluarsa',
                                default => $booking['status']
                            };
                            ?>
                            <div class="status-badge-premium <?= $statusClass ?>">
                                <?= $statusLabel ?>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body with Details Grid -->
                    <div class="booking-card-details">
                        <div class="details-grid-enhanced">
                            <div class="detail-item-enhanced">
                                <div class="detail-icon-circle">🎫</div>
                                <div class="detail-content">
                                    <div class="detail-label">Jumlah Tiket</div>
                                    <div class="detail-value"><?= number_format($booking['ticket_count']) ?> tiket</div>
                                </div>
                            </div>
                            
                            <div class="detail-item-enhanced">
                                <div class="detail-icon-circle">💰</div>
                                <div class="detail-content">
                                    <div class="detail-label">Total Harga</div>
                                    <div class="detail-value highlight">Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></div>
                                </div>
                            </div>
                            
                            <div class="detail-item-enhanced">
                                <div class="detail-icon-circle">💳</div>
                                <div class="detail-content">
                                    <div class="detail-label">Metode Pembayaran</div>
                                    <div class="detail-value"><?= esc($booking['payment_method']) ?></div>
                                </div>
                            </div>
                            
                            <div class="detail-item-enhanced">
                                <div class="detail-icon-circle">🕐</div>
                                <div class="detail-content">
                                    <div class="detail-label">Waktu Booking</div>
                                    <div class="detail-value"><?= date('d M Y H:i', strtotime($booking['booking_date'])) ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Timeline -->
                        <?php if (in_array($booking['status'], ['Waiting Payment', 'Waiting Approval', 'Lunas'])): ?>
                        <div class="status-timeline">
                            <div class="timeline-step <?= in_array($booking['status'], ['Pending', 'Waiting Payment', 'Waiting Approval', 'Lunas']) ? 'completed' : '' ?>">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <div class="timeline-title">Booking Dibuat</div>
                                    <div class="timeline-time"><?= date('d M Y H:i', strtotime($booking['booking_date'])) ?></div>
                                </div>
                            </div>
                            
                            <div class="timeline-step <?= in_array($booking['status'], ['Waiting Approval', 'Lunas']) ? 'completed' : ($booking['status'] === 'Waiting Payment' ? 'active' : '') ?>">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <div class="timeline-title">Pembayaran</div>
                                    <div class="timeline-time">
                                        <?= $booking['status'] === 'Waiting Payment' ? 'Menunggu pembayaran' : 
                                            ($booking['status'] === 'Waiting Approval' ? 'Menunggu verifikasi' : 'Selesai') ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-step <?= $booking['status'] === 'Lunas' ? 'completed' : '' ?>">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <div class="timeline-title">Terkonfirmasi</div>
                                    <div class="timeline-time">
                                        <?= $booking['status'] === 'Lunas' && !empty($booking['payment_confirmed_at']) ? 
                                            date('d M Y H:i', strtotime($booking['payment_confirmed_at'])) : 
                                            'Belum selesai' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Card Footer with Actions -->
                    <div class="booking-card-actions-premium">
                        <?php if ($booking['status'] === 'Pending' && $booking['payment_method'] === 'manual_transfer'): ?>
                            <form action="<?= base_url('payment/upload/' . $booking['id']) ?>" method="POST" enctype="multipart/form-data" class="inline-form">
                                <?= csrf_field() ?>
                                <label for="payment_proof_<?= $booking['id'] ?>" class="btn-action-premium btn-primary">
                                    <span class="btn-icon">📤</span>
                                    <span class="btn-text">Upload Bukti</span>
                                </label>
                                <input type="file" id="payment_proof_<?= $booking['id'] ?>" name="payment_proof" accept="image/*,.pdf" style="display:none" onchange="this.form.submit()">
                            </form>
                        <?php endif; ?>
                        
                        <?php if ($booking['status'] === 'Waiting Payment'): ?>
                            <a href="<?= base_url('payment/manual/' . $booking['id']) ?>" class="btn-action-premium btn-primary">
                                <span class="btn-icon">💳</span>
                                <span class="btn-text">Bayar Sekarang</span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (in_array($booking['status'], ['Pending', 'Waiting Payment'])): ?>
                            <a href="<?= base_url('user/cancelBooking/' . $booking['id']) ?>" 
                               class="btn-action-premium btn-danger" 
                               onclick="return confirm('Yakin ingin membatalkan booking ini?')">
                                <span class="btn-icon">❌</span>
                                <span class="btn-text">Cancel</span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($booking['status'] === 'Lunas'): ?>
                            <button class="btn-action-premium btn-success" onclick="alert('Download E-Ticket Coming Soon!')">
                                <span class="btn-icon">📥</span>
                                <span class="btn-text">Download E-Ticket</span>
                            </button>
                        <?php endif; ?>
                        
                        <button class="btn-action-premium btn-secondary" onclick="showBookingDetail(<?= htmlspecialchars(json_encode($booking)) ?>)">
                            <span class="btn-icon">👁️</span>
                            <span class="btn-text">Detail</span>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state-premium">
            <div class="empty-state-animation">
                <div class="empty-icon-large">📋</div>
                <div class="empty-waves">
                    <div class="wave wave-1"></div>
                    <div class="wave wave-2"></div>
                    <div class="wave wave-3"></div>
                </div>
            </div>
            <h3 class="empty-title">Belum Ada Riwayat Booking</h3>
            <p class="empty-description">Yuk mulai petualangan event kamu! Booking event favoritmu sekarang dan buat momen tak terlupakan</p>
            <a href="<?= base_url('user/dashboard') ?>" class="btn-book btn-large empty-cta">
                <span>🎉</span>
                <span>Jelajahi Event</span>
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Detail Booking -->
<div class="modal" id="detailModal">
    <div class="modal-content modal-premium">
        <div class="modal-header-premium">
            <h2>📋 Detail Booking</h2>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <div class="modal-body-premium" id="modalDetailContent">
            <!-- Content will be injected by JavaScript -->
        </div>
    </div>
</div>

<script>
// Filter Bookings
function filterBookings(event, category) {
    // Update active tab
    document.querySelectorAll('.filter-tab-enhanced').forEach(tab => {
        tab.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    
    // Filter cards
    const cards = document.querySelectorAll('.booking-card-premium');
    cards.forEach(card => {
        if (category === 'all') {
            card.style.display = 'block';
            setTimeout(() => card.style.opacity = '1', 10);
        } else {
            if (card.dataset.filterCategory === category) {
                card.style.display = 'block';
                setTimeout(() => card.style.opacity = '1', 10);
            } else {
                card.style.opacity = '0';
                setTimeout(() => card.style.display = 'none', 300);
            }
        }
    });
}

// Show Booking Detail Modal
function showBookingDetail(booking) {
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('modalDetailContent');
    
    const statusClass = booking.status === 'Lunas' ? 'status-confirmed' : 
                       booking.status === 'Pending' ? 'status-pending' : 
                       'status-waiting';
    
    content.innerHTML = `
        <div class="detail-header-premium">
            <div class="detail-event-icon">${booking.event_icon || '🎉'}</div>
            <div class="detail-event-info">
                <h3>${booking.event_title}</h3>
                <p class="detail-booking-number">#${booking.booking_number}</p>
            </div>
            <div class="status-badge-premium ${statusClass}">${booking.status}</div>
        </div>
        
        <div class="detail-grid-premium">
            <div class="detail-section-premium">
                <h4>📅 Informasi Event</h4>
                <div class="detail-row">
                    <span>Tanggal Event</span>
                    <strong>${new Date(booking.event_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</strong>
                </div>
                <div class="detail-row">
                    <span>Lokasi</span>
                    <strong>${booking.event_location}</strong>
                </div>
            </div>
            
            <div class="detail-section-premium">
                <h4>🎫 Detail Pemesanan</h4>
                <div class="detail-row">
                    <span>Jumlah Tiket</span>
                    <strong>${booking.ticket_count} tiket</strong>
                </div>
                <div class="detail-row">
                    <span>Harga per Tiket</span>
                    <strong>Rp ${parseInt(booking.price_per_ticket).toLocaleString('id-ID')}</strong>
                </div>
                <div class="detail-row total-row">
                    <span>Total Harga</span>
                    <strong class="highlight">Rp ${parseInt(booking.total_price).toLocaleString('id-ID')}</strong>
                </div>
            </div>
            
            <div class="detail-section-premium">
                <h4>💳 Pembayaran</h4>
                <div class="detail-row">
                    <span>Metode</span>
                    <strong>${booking.payment_method}</strong>
                </div>
                <div class="detail-row">
                    <span>Waktu Booking</span>
                    <strong>${new Date(booking.booking_date).toLocaleString('id-ID')}</strong>
                </div>
            </div>
        </div>
    `;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('detailModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

// Close modal on outside click
document.getElementById('detailModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// Auto-hide toast notifications
document.addEventListener('DOMContentLoaded', function() {
    const toasts = document.querySelectorAll('.toast-notification');
    toasts.forEach(toast => {
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    });
});
</script>

<?= $this->include('templates/footer') ?>