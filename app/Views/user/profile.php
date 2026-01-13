<?php
$this->setVar('showNavbar', true);
$this->setVar('bodyClass', 'dashboard-body');
?>
<?= $this->include('templates/header') ?>

<div class="container">
    <div class="dashboard-header">
        <h1>👤 Profil Saya</h1>
        <p>Kelola informasi akun kamu</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="success-box"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
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

    <div class="profile-grid">
        <!-- Profile Card -->
        <div class="profile-card-sidebar">
            <div class="profile-avatar-large">
                <span class="avatar-initial-large">
                    <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                </span>
            </div>
            <h2 class="profile-name"><?= esc($user['name'] ?? 'User') ?></h2>
            <p class="profile-email-display"><?= esc($user['email'] ?? '-') ?></p>
            <p class="profile-joined-date">Member sejak <?= date('d M Y', strtotime($user['registered_at'] ?? 'now')) ?></p>
        </div>

        <!-- Edit Form -->
        <div class="profile-edit-card">
            <h3 class="card-title">Edit Profil</h3>
            
            <form action="<?= base_url('user/profile/update') ?>" method="POST" class="profile-form">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="<?= esc($user['name'] ?? '') ?>" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= esc($user['email'] ?? '') ?>" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="phone">Nomor HP/WhatsApp</label>
                    <input type="tel" id="phone" name="phone" value="<?= esc($user['phone'] ?? '') ?>" class="form-input" required>
                </div>

                <hr class="form-divider">

                <h4 class="section-subtitle">Ganti Password (Opsional)</h4>
                <p class="form-note">Kosongkan jika tidak ingin mengubah password</p>

                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Minimal 6 karakter">
                </div>

                <div class="form-group">
                    <label for="password_confirm">Konfirmasi Password Baru</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-input" placeholder="Ketik ulang password baru">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary btn-large">💾 Simpan Perubahan</button>
                    <a href="<?= base_url('user/dashboard') ?>" class="btn-secondary btn-large">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
