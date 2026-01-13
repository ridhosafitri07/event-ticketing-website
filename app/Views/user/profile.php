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

<style>
.profile-grid {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 24px;
    margin-top: 24px;
}
.profile-card-sidebar {
    background: white;
    padding: 32px;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    text-align: center;
    height: fit-content;
}
.profile-avatar-large {
    width: 120px;
    height: 120px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.avatar-initial-large {
    font-size: 48px;
    font-weight: 700;
    color: white;
}
.profile-name {
    font-size: 24px;
    margin: 0 0 8px 0;
    color: #1a202c;
}
.profile-email-display {
    color: #64748b;
    margin: 0 0 4px 0;
    font-size: 14px;
}
.profile-joined-date {
    color: #94a3b8;
    font-size: 13px;
    margin: 0;
}
.profile-edit-card {
    background: white;
    padding: 32px;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}
.card-title {
    margin: 0 0 24px 0;
    color: #1a202c;
    font-size: 20px;
}
.section-subtitle {
    margin: 0 0 8px 0;
    color: #1a202c;
    font-size: 16px;
}
.form-divider {
    margin: 24px 0;
    border: none;
    border-top: 1px solid #e2e8f0;
}
.form-note {
    color: #64748b;
    font-size: 13px;
    margin: 0 0 20px 0;
}
.profile-form .form-group {
    margin-bottom: 20px;
}
.profile-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #334155;
    font-size: 14px;
}
.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}
.form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}
.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}
.btn-large {
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}
.btn-secondary {
    background: #e2e8f0;
    color: #334155;
}
.btn-secondary:hover {
    background: #cbd5e1;
}
@media (max-width: 768px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?= $this->include('templates/footer') ?>
