<?php

$title = $title ?? 'Settings - EventKu';
$errors = session('errors') ?? [];

$extraStyles = <<<CSS
    .admin-container { max-width: 1100px; margin: 0 auto; }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
    }
    .page-header h1 { font-size: 22px; color: var(--gray-900); margin: 0; }

    .grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .card {
        background: white;
        border-radius: 14px;
        border: 1px solid var(--gray-200);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }
    .card-header {
        padding: 16px 18px;
        border-bottom: 1px solid var(--gray-100);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .card-title { font-size: 14px; font-weight: 900; color: var(--gray-900); margin: 0; }
    .card-body { padding: 18px; }

    .form {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        align-items: start;
    }

    .field { display: flex; flex-direction: column; gap: 8px; }
    .field label { font-size: 12px; font-weight: 900; color: var(--gray-600); text-transform: uppercase; letter-spacing: .5px; }
    .field input {
        padding: 12px 14px;
        border: 1px solid var(--gray-300);
        border-radius: 12px;
        font-size: 14px;
        background: white;
    }
    .field input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }
    .hint { font-size: 12px; color: var(--gray-500); font-weight: 700; }

    .actions { display: flex; gap: 10px; justify-content: flex-end; grid-column: 1 / -1; }
    .btn {
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 900;
        border: 1px solid var(--gray-200);
        background: white;
        cursor: pointer;
        text-decoration: none;
        color: var(--gray-800);
        transition: all 0.2s ease;
    }
    .btn:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08); }
    .btn-primary { background: var(--primary); border-color: transparent; color: white; }
    .btn-primary:hover { background: var(--primary-dark); }

    .alert {
        border-radius: 14px;
        padding: 14px 16px;
        border: 1px solid var(--gray-200);
        margin-bottom: 14px;
        font-weight: 800;
    }
    .alert-success { background: rgba(16, 185, 129, 0.10); border-color: rgba(16, 185, 129, 0.25); color: #0f766e; }
    .alert-danger { background: rgba(239, 68, 68, 0.10); border-color: rgba(239, 68, 68, 0.25); color: #b91c1c; }

    .kv {
        display: grid;
        grid-template-columns: 160px 1fr;
        gap: 10px 14px;
        align-items: center;
    }
    .k { color: var(--gray-500); font-weight: 900; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
    .v { color: var(--gray-900); font-weight: 900; font-size: 13px; }

    @media (max-width: 900px) {
        .form { grid-template-columns: 1fr; }
        .kv { grid-template-columns: 1fr; }
        .actions { justify-content: stretch; }
        .btn { width: 100%; }
    }
CSS;

$this->setVar('title', $title);
$this->setVar('activePage', 'settings');
$this->setVar('extraStyles', $extraStyles);
?>

<?= $this->include('admin/_partials/layout_top') ?>

<div class="admin-container">
    <div class="page-header">
        <h1>⚙️ Settings</h1>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $msg): ?>
                <div><?= esc($msg) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="grid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">👤 Profil Admin</h3>
                <span class="hint">Terhubung sebagai: <?= esc($admin['username'] ?? '-') ?></span>
            </div>
            <div class="card-body">
                <div class="kv" style="margin-bottom: 16px;">
                    <div class="k">Role</div>
                    <div class="v"><?= esc($admin['role'] ?? 'admin') ?></div>
                </div>

                <form class="form" action="<?= base_url('admin/settings/profile') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="field">
                        <label>Full Name</label>
                        <input type="text" name="full_name" value="<?= esc($admin['full_name'] ?? '') ?>" required>
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= esc($admin['email'] ?? '') ?>" required>
                    </div>
                    <div class="actions">
                        <button type="submit" class="btn btn-primary">Update Profil</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">🔒 Ganti Password</h3>
                <span class="hint">Minimal 6 karakter</span>
            </div>
            <div class="card-body">
                <form class="form" action="<?= base_url('admin/settings/password') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="field">
                        <label>Password Saat Ini</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="field"></div>

                    <div class="field">
                        <label>Password Baru</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="field">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" required>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Ganti password sekarang?');">Ganti Password</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">🧩 Sistem</h3>
            </div>
            <div class="card-body">
                <div class="kv">
                    <div class="k">App</div>
                    <div class="v">EventKu Admin</div>
                    <div class="k">PHP</div>
                    <div class="v"><?= esc(PHP_VERSION) ?></div>
                    <div class="k">Environment</div>
                    <div class="v"><?= esc(ENVIRONMENT ?? 'production') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/_partials/layout_bottom') ?>
