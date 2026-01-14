<?php
$this->setVar('showNavbar', true);
$this->setVar('bodyClass', 'dashboard-body');
?>
<?= $this->include('templates/header') ?>

<div class="container" style="max-width: 900px; margin: 0 auto; padding: 28px 16px;">
    <h1 style="font-size: 28px; margin-bottom: 6px;">🏦 Transfer Manual</h1>
    <p style="color: #64748b; margin-bottom: 18px;">Lakukan transfer, lalu upload bukti agar admin bisa verifikasi.</p>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="success-box" style="margin-bottom: 14px;"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="error-box" style="margin-bottom: 14px;"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="error-box" style="margin-bottom: 14px;">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <p>✗ <?= esc($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px; margin-bottom: 16px;">
        <div style="display:flex; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
            <div>
                <div style="font-weight: 700;">Booking #<?= esc($booking['booking_number']) ?></div>
                <div style="color:#64748b; font-size: 14px;"><?= esc($booking['event_title']) ?></div>
            </div>
            <div style="font-weight: 800; color:#667eea; font-size: 16px;">Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr; gap: 16px;">
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:18px;">
            <h3 style="margin-bottom: 10px;">1) Transfer ke rekening</h3>
            <ul style="margin-left: 18px; color:#334155;">
                <?php foreach (($bankAccounts ?? []) as $acc): ?>
                    <li style="margin-bottom: 8px;">
                        <strong><?= esc($acc['bank']) ?></strong> — <?= esc($acc['account_number']) ?> a.n <?= esc($acc['account_name']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p style="color:#64748b; font-size: 13px; margin-top: 10px;">Catatan: setelah transfer, screenshot/ambil foto bukti lalu upload di langkah 2.</p>
        </div>

        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:18px;">
            <h3 style="margin-bottom: 10px;">2) Upload bukti transfer</h3>

            <?php if (!empty($paymentProof)): ?>
                <div style="padding: 12px; background: #f1f5f9; border-radius: 10px; margin-bottom: 12px;">
                    <div style="font-weight: 700; margin-bottom: 4px;">Bukti sudah diupload</div>
                    <div style="color:#64748b; font-size: 13px;">File: <?= esc($paymentProof['file_name'] ?? '-') ?></div>
                    <div style="color:#64748b; font-size: 13px;">Status booking: <strong><?= esc($booking['status']) ?></strong></div>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('payment/upload/' . $booking['id']) ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="file" name="payment_proof" accept="image/*,.pdf" required style="display:block; margin-bottom: 12px;">
                <button type="submit" class="btn-book btn-large" style="width: 100%; max-width: 360px;">
                    📤 Upload Bukti
                </button>
                <a href="<?= base_url('user/riwayat') ?>" class="btn-secondary btn-large" style="display:inline-block; margin-left: 8px;">
                    Nanti saja
                </a>
            </form>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
