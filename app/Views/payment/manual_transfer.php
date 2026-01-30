<?php
$this->setVar('showNavbar', true);
$this->setVar('bodyClass', 'dashboard-body');
?>
<?= $this->include('templates/header') ?>

<div class="container payment-container-premium">
    <!-- Hero Header -->
    <div class="payment-hero">
        <div class="payment-hero-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                <path d="M21 4H3C2.44772 4 2 4.44772 2 5V19C2 19.5523 2.44772 20 3 20H21C21.5523 20 22 19.5523 22 19V5C22 4.44772 21.5523 4 21 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 10H22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1 class="payment-title">Transfer Manual</h1>
        <p class="payment-subtitle">Lakukan transfer, lalu upload bukti agar admin bisa verifikasi.</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert-premium success">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert-premium error">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <!-- Booking Summary Card -->
    <div class="booking-card-premium">
        <div class="booking-header">
            <div class="booking-info">
                <div class="booking-number">Booking #<?= esc($booking['booking_number']) ?></div>
                <div class="booking-event"><?= esc($booking['event_title']) ?></div>
            </div>
            <div class="booking-price">
                <div class="price-label">Total</div>
                <div class="price-amount">Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></div>
            </div>
        </div>
        
        <?php if (!empty($booking['payment_deadline'])): ?>
            <?php 
                $deadline = strtotime($booking['payment_deadline']);
                $now = time();
                $timeLeft = $deadline - $now;
                $isExpired = $timeLeft <= 0;
                $isUrgent = $timeLeft > 0 && $timeLeft <= 3600; // 1 jam
            ?>
            
            <?php if ($isExpired): ?>
                <div class="deadline-warning expired">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 8V12M12 16H12.01M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div>
                        <div class="deadline-title">⏰ Waktu Pembayaran Habis!</div>
                        <div class="deadline-text">Booking ini akan otomatis dibatalkan. Silakan booking ulang jika masih ingin mengikuti event ini.</div>
                    </div>
                </div>
            <?php elseif ($isUrgent): ?>
                <div class="deadline-warning urgent">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 8V12M12 16H12.01M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div class="deadline-content">
                        <div class="deadline-title">⚠️ Segera Selesaikan Pembayaran!</div>
                        <div class="deadline-text">Batas waktu pembayaran:</div>
                        <div class="deadline-timer" id="countdown" data-deadline="<?= $booking['payment_deadline'] ?>"></div>
                    </div>
                </div>
            <?php else: ?>
                <div class="deadline-info">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <div>
                        <div class="deadline-label">Batas Waktu Pembayaran:</div>
                        <div class="deadline-timer" id="countdown" data-deadline="<?= $booking['payment_deadline'] ?>"></div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="payment-steps">
        <!-- Step 1: Transfer -->
        <div class="step-card">
            <div class="step-header">
                <div class="step-number">1</div>
                <h3 class="step-title">Transfer ke rekening</h3>
            </div>
            <div class="step-content">
                <div class="bank-accounts">
                    <?php foreach (($bankAccounts ?? []) as $acc): ?>
                        <div class="bank-account-item">
                            <div class="bank-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="bank-details">
                                <div class="bank-name"><?= esc($acc['bank']) ?></div>
                                <div class="bank-number"><?= esc($acc['account_number']) ?></div>
                                <div class="bank-holder">a.n <?= esc($acc['account_name']) ?></div>
                            </div>
                            <button class="btn-copy" onclick="copyToClipboard('<?= esc($acc['account_number']) ?>')">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M8 4V16C8 16.5304 8.21071 17.0391 8.58579 17.4142C8.96086 17.7893 9.46957 18 10 18H18C18.5304 18 19.0391 17.7893 19.4142 17.4142C19.7893 17.0391 20 16.5304 20 16V7.242C20 6.97556 19.9467 6.71181 19.8433 6.46624C19.7399 6.22068 19.5885 5.99824 19.398 5.812L16.083 2.57C15.7094 2.20466 15.2076 2.00007 14.685 2H10C9.46957 2 8.96086 2.21071 8.58579 2.58579C8.21071 2.96086 8 3.46957 8 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M16 18V20C16 20.5304 15.7893 21.0391 15.4142 21.4142C15.0391 21.7893 14.5304 22 14 22H6C5.46957 22 4.96086 21.7893 4.58579 21.4142C4.21071 21.0391 4 20.5304 4 20V9C4 8.46957 4.21071 7.96086 4.58579 7.58579C4.96086 7.21071 5.46957 7 6 7H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Copy
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="step-note">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M12 16V12M12 8H12.01M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Catatan: Setelah transfer, screenshot/ambil foto bukti lalu upload di langkah 2.
                </div>
            </div>
        </div>

        <!-- Step 2: Upload -->
        <div class="step-card">
            <div class="step-header">
                <div class="step-number">2</div>
                <h3 class="step-title">Upload bukti transfer</h3>
            </div>
            <div class="step-content">
                <?php if (!empty($paymentProof)): ?>
                    <div class="upload-status success">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div>
                            <div class="status-title">Bukti sudah diupload</div>
                            <div class="status-details">File: <?= esc($paymentProof['file_name'] ?? '-') ?></div>
                            <?php if (!empty($paymentProof['payment_date'])): ?>
                                <div class="status-details">Tanggal Transfer: <strong><?= date('d M Y H:i', strtotime($paymentProof['payment_date'])) ?></strong></div>
                            <?php endif; ?>
                            <div class="status-details">Status: <strong><?= esc($booking['status']) ?></strong></div>
                            <?php if ($booking['status'] === 'Dibatalkan' && !empty($booking['payment_details'])): ?>
                                <?php 
                                    $paymentDetails = json_decode($booking['payment_details'], true);
                                    if (!empty($paymentDetails['reason'])):
                                ?>
                                    <div class="status-details" style="color: #ef4444; font-weight: 600; margin-top: 8px; padding: 8px; background: rgba(239, 68, 68, 0.1); border-radius: 6px;">
                                        ❌ Alasan Penolakan: <?= esc($paymentDetails['reason']) ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($paymentProof['file_path']) && in_array($paymentProof['file_type'], ['image/jpeg', 'image/jpg', 'image/png'])): ?>
                        <div style="margin-top: 16px; text-align: center;">
                            <div style="font-weight: 600; margin-bottom: 8px; color: #64748b;">Preview Bukti Transfer:</div>
                            <img src="<?= base_url($paymentProof['file_path']) ?>" 
                                 alt="Bukti Transfer" 
                                 style="max-width: 100%; height: auto; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); cursor: pointer;"
                                 onclick="window.open('<?= base_url($paymentProof['file_path']) ?>', '_blank')">
                            <div style="font-size: 12px; color: #94a3b8; margin-top: 8px;">💡 Klik untuk memperbesar</div>
                        </div>
                    <?php endif; ?>
                    
                    <div style="margin-top: 20px; text-align: center;">
                        <a href="<?= base_url('user/riwayat') ?>" class="btn-secondary-premium" style="display: inline-block; text-decoration: none;">
                            Lihat Riwayat Booking
                        </a>
                    </div>
                    
                <?php else: ?>

                <form action="<?= base_url('payment/upload/' . $booking['id']) ?>" method="POST" enctype="multipart/form-data" class="upload-form">
                    <?= csrf_field() ?>
                    
                    <div class="file-upload-wrapper">
                        <input type="file" name="payment_proof" id="payment_proof" accept="image/*,.pdf" required class="file-input">
                        <label for="payment_proof" class="file-label">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                                <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M17 8L12 3L7 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 3V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="file-text">Klik untuk pilih file</span>
                            <span class="file-hint">Format: JPG, PNG, atau PDF (Max 5MB)</span>
                        </label>
                        <div id="imagePreview" style="margin-top: 16px; display: none; text-align: center;">
                            <div style="font-weight: 600; margin-bottom: 8px; color: #64748b;">Preview:</div>
                            <img id="previewImg" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-upload-premium">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M17 8L12 3L7 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 3V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Upload Bukti
                        </button>
                        <a href="<?= base_url('user/riwayat') ?>" class="btn-secondary-premium">
                            Nanti saja
                        </a>
                    </div>
                </form>
                
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Berhasil', 'Nomor rekening disalin!', 'success');
    });
}

function showToast(title, message, type) {
    const toast = document.createElement('div');
    toast.className = `toast-notification-modern ${type}`;
    toast.innerHTML = `
        <div class="toast-wrapper">
            <div class="toast-icon-modern">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="toast-content-modern">
                <div class="toast-title-modern">${title}</div>
                <div class="toast-message-modern">${message}</div>
            </div>
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Countdown Timer
function updateCountdown() {
    const countdownEl = document.getElementById('countdown');
    if (!countdownEl) return;
    
    // Stop countdown jika bukti sudah diupload
    const uploadStatus = document.querySelector('.upload-status.success');
    if (uploadStatus) {
        countdownEl.innerHTML = '<span style="color: #10b981; font-weight: 800;">✓ Bukti telah diupload</span>';
        return;
    }
    
    const deadline = new Date(countdownEl.dataset.deadline).getTime();
    const now = new Date().getTime();
    const timeLeft = deadline - now;
    
    if (timeLeft <= 0) {
        countdownEl.innerHTML = '<span style="color: #ef4444; font-weight: 800;">EXPIRED - Waktu Habis!</span>';
        // Reload halaman setelah 2 detik agar status ter-update
        setTimeout(() => location.reload(), 2000);
        return;
    }
    
    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
    const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
    
    let timeString = '';
    if (days > 0) {
        timeString = `${days} hari ${hours} jam ${minutes} menit`;
    } else if (hours > 0) {
        timeString = `${hours} jam ${minutes} menit ${seconds} detik`;
    } else {
        timeString = `${minutes} menit ${seconds} detik`;
    }
    
    // Ubah warna jika < 1 jam
    const color = timeLeft < 3600000 ? '#ef4444' : '#10b981';
    countdownEl.innerHTML = `<span style="color: ${color}; font-weight: 800; font-size: 1.1em;">${timeString}</span>`;
}

// Update countdown setiap detik
if (document.getElementById('countdown')) {
    updateCountdown();
    const uploadStatus = document.querySelector('.upload-status.success');
    if (!uploadStatus) {
        setInterval(updateCountdown, 1000);
    }
}

// Show selected file name
document.getElementById('payment_proof')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileName = file.name;
        const fileText = document.querySelector('.file-text');
        if (fileText) {
            fileText.textContent = fileName;
            fileText.style.color = '#10b981';
        }
        
        // Show image preview if it's an image
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImg');
                if (preview && previewImg) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                }
            };
            reader.readAsDataURL(file);
        } else {
            // Hide preview for non-image files
            const preview = document.getElementById('imagePreview');
            if (preview) {
                preview.style.display = 'none';
            }
        }
    }
});
</script>

<?= $this->include('templates/footer') ?>
