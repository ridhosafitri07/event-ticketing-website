<?= $this->include('templates/header') ?>

<div class="particle"></div>
<div class="particle"></div>
<div class="particle"></div>

<div class="auth-container" style="position: relative; z-index: 1;">
    <div class="auth-header">
        <div class="auth-icon">🔐</div>
        <h1>Admin Login</h1>
        <p>Masuk ke panel admin EventKu</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">✓ <?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">✗ <?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-error">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <p>✗ <?= esc($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/doLogin') ?>" method="POST" class="auth-form">
        <?= csrf_field() ?>
        
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Masukkan username admin" value="<?= old('username') ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Masukkan password" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Login Admin</button>
    </form>

    <div class="auth-footer">
        <p><a href="<?= base_url('/') ?>" class="link-primary">← Kembali ke halaman utama</a></p>
    </div>
</div>

<?= $this->section('scripts') ?>
<style>
body {
    background: #0F172A;
    height: 100vh;
    padding: 0;
    margin: 0;
    color: #F1F5F9;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}
body::before {
    content: '';
    position: fixed;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at 20% 50%, rgba(124, 58, 237, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(236, 72, 153, 0.15) 0%, transparent 50%);
    animation: gradientShift 15s ease infinite;
    z-index: 0;
    pointer-events: none;
}
@keyframes gradientShift {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    33% { transform: translate(5%, 5%) rotate(120deg); }
    66% { transform: translate(-5%, 5%) rotate(240deg); }
}
.particle {
    position: fixed;
    width: 4px;
    height: 4px;
    background: #7C3AED;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
    opacity: 0;
    animation: float-particle 20s linear infinite;
    box-shadow: 0 0 10px #7C3AED;
}
@keyframes float-particle {
    0% { opacity: 0; transform: translateY(100vh) translateX(0); }
    10% { opacity: 0.5; }
    90% { opacity: 0.5; }
    100% { opacity: 0; transform: translateY(-100vh) translateX(100px); }
}
.particle:nth-child(1) { left: 10%; animation-delay: 0s; }
.particle:nth-child(2) { left: 50%; animation-delay: 7s; background: #EC4899; box-shadow: 0 0 10px #EC4899; }
.particle:nth-child(3) { left: 90%; animation-delay: 14s; }
.auth-container {
    max-width: 440px;
    width: 100%;
    background: rgba(30, 41, 59, 0.7);
    backdrop-filter: blur(20px);
    padding: 48px;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(148, 163, 184, 0.1) inset;
    border: 1px solid rgba(148, 163, 184, 0.1);
}
.auth-header { text-align: center; margin-bottom: 32px; }
.auth-icon { font-size: 48px; margin-bottom: 16px; }
.auth-header h1 { font-size: 28px; font-weight: 700; color: #F1F5F9; margin-bottom: 8px; }
.auth-header p { color: #94A3B8; font-size: 15px; }
.alert {
    padding: 14px 18px;
    border-radius: 12px;
    margin-bottom: 24px;
    font-size: 14px;
}
.alert-success {
    background: rgba(34, 197, 94, 0.15);
    border: 1px solid rgba(34, 197, 94, 0.3);
    color: #86efac;
}
.alert-error {
    background: rgba(239, 68, 68, 0.15);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #fca5a5;
}
.form-group { margin-bottom: 20px; }
.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #E2E8F0;
    font-size: 14px;
}
.form-group input {
    width: 100%;
    padding: 14px 16px;
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 12px;
    background: rgba(15, 23, 42, 0.5);
    color: #F1F5F9;
    font-size: 15px;
    transition: all 0.3s ease;
}
.form-group input:focus {
    outline: none;
    border-color: #7C3AED;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}
.btn {
    padding: 14px 24px;
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}
.btn-primary {
    background: linear-gradient(135deg, #7C3AED 0%, #EC4899 100%);
    color: white;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(124, 58, 237, 0.4);
}
.btn-block { width: 100%; }
.auth-footer {
    text-align: center;
    margin-top: 24px;
    color: #CBD5E1;
    font-size: 14px;
}
.link-primary { color: #A78BFA; text-decoration: none; font-weight: 600; }
.link-primary:hover { color: #C4B5FD; }
</style>
<?= $this->endSection() ?>

<?= $this->include('templates/footer') ?>
