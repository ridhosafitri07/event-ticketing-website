<?php
$title = $title ?? 'Bukti Pembayaran - EventKu';
$extraStyles = <<<CSS
    .admin-container { max-width: 1200px; margin: 0 auto; }
    .proof-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }
    .proof-card {
        background: white;
        border-radius: 14px;
        padding: 24px;
        border: 1px solid var(--gray-200);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
    }
    .proof-card h2 {
        font-size: 18px;
        color: var(--gray-900);
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--gray-100);
    }
    .detail-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid var(--gray-100);
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-label { color: var(--gray-500); font-weight: 800; font-size: 13px; }
    .detail-value { color: var(--gray-900); font-weight: 900; font-size: 13px; text-align: right; }

    .proof-image-container {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: 14px;
        padding: 18px;
        text-align: center;
    }
    .proof-image-container img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    .proof-image-container img:hover { transform: scale(1.01); }
    .proof-info {
        margin-top: 12px;
        padding: 10px;
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        font-size: 13px;
        color: var(--gray-500);
    }
    .action-buttons { display: flex; gap: 12px; margin-top: 18px; }
    .btn-full { flex: 1; justify-content: center; padding: 12px 18px; }
    .btn-success { background: var(--success); color: white; }
    .btn-success:hover { filter: brightness(0.92); }
    .status-badge {
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        display: inline-block;
    }
    .status-waiting-approval { background: #dbeafe; color: #1e40af; }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--primary);
        text-decoration: none;
        font-weight: 900;
        margin-bottom: 18px;
    }
    .back-link:hover { color: var(--primary-dark); }
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal.active { display: flex; }
    .modal img { max-width: 90%; max-height: 90vh; border-radius: 12px; }
    .modal-close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: white;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
    }
    @media (max-width: 900px) { .proof-container { grid-template-columns: 1fr; } }
CSS;

$this->setVar('title', $title);
$this->setVar('activePage', 'bookings');
$this->setVar('extraStyles', $extraStyles);
?>

<?= $this->include('admin/_partials/layout_top') ?>

<div class="admin-container">
    <a href="<?= base_url('admin/bookings') ?>" class="back-link">
        ← Kembali ke Bookings
    </a>

    <h1 style="margin-bottom: 32px; color: #1e293b;">📸 Bukti Pembayaran</h1>

    <div class="proof-container">
        <!-- Booking Details -->
        <div class="proof-card">
            <h2>📋 Detail Booking</h2>
            
            <div class="detail-row">
                <span class="detail-label">Booking Number</span>
                <span class="detail-value"><?= esc($booking['booking_number']) ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Event</span>
                <span class="detail-value"><?= esc($booking['event_title']) ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Tanggal Event</span>
                <span class="detail-value"><?= esc($booking['event_date']) ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Lokasi</span>
                <span class="detail-value"><?= esc($booking['event_location']) ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Jumlah Tiket</span>
                <span class="detail-value"><?= $booking['ticket_count'] ?> tiket</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Total Harga</span>
                <span class="detail-value" style="color: #667eea; font-size: 18px;">
                    Rp <?= number_format($booking['total_price'], 0, ',', '.') ?>
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value">
                    <span class="status-badge status-waiting-approval">
                        <?= esc($booking['status']) ?>
                    </span>
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Tanggal Booking</span>
                <span class="detail-value"><?= date('d M Y H:i', strtotime($booking['booking_date'])) ?></span>
            </div>
            
            <div style="margin-top: 24px; padding-top: 24px; border-top: 2px solid #f1f5f9;">
                <h3 style="font-size: 16px; color: #1e293b; margin-bottom: 12px;">📄 Info File</h3>
                <div class="detail-row">
                    <span class="detail-label">Nama File</span>
                    <span class="detail-value" style="font-size: 12px;"><?= esc($paymentProof['file_name']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tipe File</span>
                    <span class="detail-value"><?= esc($paymentProof['file_type']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Ukuran File</span>
                    <span class="detail-value"><?= round($paymentProof['file_size'] / 1024, 2) ?> KB</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Upload</span>
                    <span class="detail-value"><?= date('d M Y H:i', strtotime($paymentProof['uploaded_at'])) ?></span>
                </div>
            </div>
        </div>

        <!-- Payment Proof Image -->
        <div class="proof-card">
            <h2>🖼️ Bukti Transfer</h2>
            
            <div class="proof-image-container">
                <?php if (in_array($paymentProof['file_type'], ['image/jpeg', 'image/jpg', 'image/png'])): ?>
                    <img src="<?= base_url($paymentProof['file_path']) ?>" 
                         alt="Bukti Transfer" 
                         onclick="openImageModal(this.src)">
                    <div class="proof-info">
                        💡 Klik gambar untuk memperbesar
                    </div>
                <?php elseif ($paymentProof['file_type'] === 'application/pdf'): ?>
                    <div style="padding: 40px; text-align: center;">
                        <div style="font-size: 64px; margin-bottom: 16px;">📄</div>
                        <p style="color: #64748b; margin-bottom: 20px;">File PDF</p>
                        <a href="<?= base_url($paymentProof['file_path']) ?>" 
                           target="_blank" 
                           class="btn btn-secondary" 
                           style="display: inline-block;">
                            📥 Download PDF
                        </a>
                    </div>
                <?php else: ?>
                    <div style="padding: 40px; text-align: center; color: #ef4444;">
                        ⚠️ Format file tidak didukung
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Action Buttons -->
            <?php if ($booking['status'] === 'Waiting Approval'): ?>
                <div class="action-buttons">
                    <form action="<?= base_url('admin/approve/' . $booking['id']) ?>" method="POST" style="flex: 1;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success" onclick="return confirm('✅ Approve pembayaran ini?\n\nBooking: <?= esc($booking['booking_number']) ?>\nTotal: Rp <?= number_format($booking['total_price'], 0, ',', '.') ?>')">
                            ✓ Approve Pembayaran
                        </button>
                    </form>
                    
                    <button type="button" class="btn btn-danger" onclick="showRejectModal()">
                        ✗ Reject Pembayaran
                    </button>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 20px; background: #f8fafc; border-radius: 8px; margin-top: 24px;">
                    <p style="color: #64748b; margin: 0;">
                        Status booking sudah <strong><?= esc($booking['status']) ?></strong>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal" id="imageModal" onclick="closeImageModal()">
    <span class="modal-close">&times;</span>
    <img id="modalImage" src="" alt="Bukti Transfer">
</div>

<!-- Reject Modal -->
<div class="modal" id="rejectModal" style="background: rgba(0, 0, 0, 0.5);">
    <div style="background: white; padding: 32px; border-radius: 12px; max-width: 500px; width: 90%;" onclick="event.stopPropagation()">
        <h3 style="margin-bottom: 20px; color: #1e293b;">❌ Reject Pembayaran</h3>
        <form action="<?= base_url('admin/reject/' . $booking['id']) ?>" method="POST">
            <?= csrf_field() ?>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">
                    Alasan Penolakan *
                </label>
                <textarea name="reject_reason" 
                          rows="4" 
                          placeholder="Contoh: Bukti transfer tidak valid, nominal tidak sesuai, transfer ke rekening salah..."
                          required
                          style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-family: inherit;"></textarea>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeRejectModal()" style="flex: none; padding: 12px 24px;">
                    Batal
                </button>
                <button type="submit" class="btn btn-danger" style="flex: none; padding: 12px 24px;">
                    Reject Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

function showRejectModal() {
    document.getElementById('rejectModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
        closeRejectModal();
    }
});
</script>

<?= $this->include('admin/_partials/layout_bottom') ?>