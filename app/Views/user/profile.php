<?php
$this->setVar('showNavbar', true);
$this->setVar('bodyClass', 'dashboard-body');
?>
<?= $this->include('templates/header') ?>

<style>
.profile-container-clean {
    max-width: 1100px;
    margin: 0 auto;
    padding: 30px 20px;
}

.profile-header-clean {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 24px;
}

.profile-avatar-clean {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    font-weight: 900;
    color: white;
    flex-shrink: 0;
}

.profile-header-info {
    flex: 1;
}

.profile-header-name {
    font-size: 26px;
    font-weight: 900;
    color: white;
    margin: 0 0 6px 0;
}

.profile-header-meta {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.6);
    margin: 0;
}

.stats-grid-clean {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.stat-box-clean {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 14px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
}

.stat-box-clean:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(168, 85, 247, 0.4);
    transform: translateY(-2px);
}

.stat-icon-clean {
    font-size: 28px;
    margin-bottom: 8px;
}

.stat-label-clean {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.5);
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}

.stat-value-clean {
    font-size: 24px;
    font-weight: 900;
    color: white;
}

.info-section-clean {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 14px;
    padding: 28px;
    margin-bottom: 16px;
}

.section-header-clean {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.section-title-clean {
    font-size: 18px;
    font-weight: 900;
    color: white;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-grid-clean {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.info-item-clean {
    background: rgba(255, 255, 255, 0.03);
    padding: 16px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.info-label-clean {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.5);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.info-value-clean {
    font-size: 15px;
    color: white;
    font-weight: 600;
}

.btn-clean {
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 800;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-purple {
    background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);
    color: white;
}

.btn-purple:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(168, 85, 247, 0.4);
}

.btn-dark {
    background: rgba(255, 255, 255, 0.08);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.btn-dark:hover {
    background: rgba(255, 255, 255, 0.12);
}

/* Modal Styles */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
    z-index: 9998;
    animation: fadeIn 0.3s ease;
}

.modal-overlay.active {
    display: block;
}

.modal-container {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    background: #1a1f2e;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 30px;
    z-index: 9999;
    animation: slideUp 0.3s ease;
}

.modal-container.active {
    display: block;
}

/* Custom scrollbar for modal */
.modal-container::-webkit-scrollbar {
    width: 8px;
}

.modal-container::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}

.modal-container::-webkit-scrollbar-thumb {
    background: rgba(168, 85, 247, 0.5);
    border-radius: 10px;
}

.modal-container::-webkit-scrollbar-thumb:hover {
    background: rgba(168, 85, 247, 0.7);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.modal-title {
    font-size: 22px;
    font-weight: 900;
    color: white;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-close {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 20px;
}

.modal-close:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: rotate(90deg);
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translate(-50%, -40%);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}

.form-group-clean {
    margin-bottom: 20px;
}

.form-label-clean {
    display: block;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 700;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-input-clean {
    width: 100%;
    padding: 12px 16px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    font-size: 15px;
    color: white;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.form-input-clean:focus {
    outline: none;
    border-color: #a855f7;
    background: rgba(255, 255, 255, 0.08);
    box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
}

.form-input-clean::placeholder {
    color: rgba(255, 255, 255, 0.3);
}

.form-actions-clean {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}

.alert-clean {
    padding: 14px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 600;
    animation: slideDown 0.3s ease;
}

.alert-success {
    background: rgba(34, 197, 94, 0.15);
    border: 1px solid rgba(34, 197, 94, 0.3);
    color: #4ade80;
}

.alert-error {
    background: rgba(239, 68, 68, 0.15);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #f87171;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .profile-header-clean {
        flex-direction: column;
        text-align: center;
    }
    
    .section-header-clean {
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
    }
    
    .form-actions-clean {
        flex-direction: column;
    }
    
    .btn-clean {
        width: 100%;
        justify-content: center;
    }
    
    .modal-container {
        width: 95%;
        padding: 20px;
    }
    
    .info-grid-clean {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function openModal(modalId) {
    document.getElementById(modalId + 'Overlay').classList.add('active');
    document.getElementById(modalId + 'Modal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId + 'Overlay').classList.remove('active');
    document.getElementById(modalId + 'Modal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking overlay
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                const modalId = this.id.replace('Overlay', '');
                closeModal(modalId);
            }
        });
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(overlay => {
                const modalId = overlay.id.replace('Overlay', '');
                closeModal(modalId);
            });
        }
    });
});
</script>

<div class="profile-container-clean">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert-clean alert-success">
            ✅ <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert-clean alert-error">
            ❌ <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert-clean alert-error">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <div>❌ <?= esc($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Profile Header -->
    <div class="profile-header-clean">
        <div class="profile-avatar-clean">
            <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
        </div>
        <div class="profile-header-info">
            <h1 class="profile-header-name"><?= esc($user['name'] ?? 'User') ?></h1>
            <p class="profile-header-meta">⭐ Member sejak <?= date('d M Y', strtotime($user['registered_at'] ?? 'now')) ?></p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid-clean">
        <div class="stat-box-clean">
            <div class="stat-icon-clean">🎫</div>
            <div class="stat-label-clean">Total Booking</div>
            <div class="stat-value-clean"><?= $totalBookings ?? 0 ?></div>
        </div>
        
        <div class="stat-box-clean">
            <div class="stat-icon-clean">🎉</div>
            <div class="stat-label-clean">Event Diikuti</div>
            <div class="stat-value-clean"><?= $attendedEvents ?? 0 ?></div>
        </div>
        
        <div class="stat-box-clean">
            <div class="stat-icon-clean">💰</div>
            <div class="stat-label-clean">Total Pengeluaran</div>
            <div class="stat-value-clean">Rp <?= number_format($totalSpent ?? 0, 0, ',', '.') ?></div>
        </div>
    </div>

    <!-- Informasi Akun -->
    <div class="info-section-clean">
        <div class="section-header-clean">
            <h2 class="section-title-clean">
                📋 Informasi Akun
            </h2>
            <button onclick="openModal('editProfile')" class="btn-clean btn-purple">
                ✏️ Edit Profil
            </button>
        </div>
        
        <div class="info-grid-clean">
            <div class="info-item-clean">
                <div class="info-label-clean">
                    👤 Nama Lengkap
                </div>
                <div class="info-value-clean"><?= esc($user['name'] ?? '-') ?></div>
            </div>
            
            <div class="info-item-clean">
                <div class="info-label-clean">
                    📧 Email
                </div>
                <div class="info-value-clean"><?= esc($user['email'] ?? '-') ?></div>
            </div>
            
            <div class="info-item-clean">
                <div class="info-label-clean">
                    📱 Nomor HP/WhatsApp
                </div>
                <div class="info-value-clean"><?= esc($user['phone'] ?? '-') ?></div>
            </div>
        </div>
    </div>

    <!-- Keamanan Akun -->
    <div class="info-section-clean">
        <div class="section-header-clean">
            <h2 class="section-title-clean">
                🔒 Keamanan Akun
            </h2>
            <button onclick="openModal('changePassword')" class="btn-clean btn-purple">
                🔑 Ganti Password
            </button>
        </div>
        
        <p style="color: rgba(255,255,255,0.6); font-size: 14px; margin: 0;">
            Jaga keamanan akun kamu dengan mengubah password secara berkala
        </p>
    </div>
</div>

<!-- Edit Profile Modal -->
<div id="editProfileOverlay" class="modal-overlay"></div>
<div id="editProfileModal" class="modal-container">
    <div class="modal-header">
        <h2 class="modal-title">✏️ Edit Profil</h2>
        <button class="modal-close" onclick="closeModal('editProfile')">✕</button>
    </div>
    
    <form action="<?= base_url('user/profile/update') ?>" method="POST">
        <?= csrf_field() ?>
        
        <div class="form-group-clean">
            <label class="form-label-clean">👤 Nama Lengkap</label>
            <input type="text" name="name" value="<?= esc($user['name'] ?? '') ?>" 
                   class="form-input-clean" required placeholder="Masukkan nama lengkap">
        </div>

        <div class="form-group-clean">
            <label class="form-label-clean">📧 Email</label>
            <input type="email" name="email" value="<?= esc($user['email'] ?? '') ?>" 
                   class="form-input-clean" required placeholder="contoh@email.com">
        </div>

        <div class="form-group-clean">
            <label class="form-label-clean">📱 Nomor HP/WhatsApp</label>
            <input type="tel" name="phone" value="<?= esc($user['phone'] ?? '') ?>" 
                   class="form-input-clean" required placeholder="08xx-xxxx-xxxx">
        </div>

        <div class="form-actions-clean">
            <button type="submit" class="btn-clean btn-purple">
                💾 Simpan Perubahan
            </button>
            <button type="button" onclick="closeModal('editProfile')" class="btn-clean btn-dark">
                ✕ Batal
            </button>
        </div>
    </form>
</div>

<!-- Change Password Modal -->
<div id="changePasswordOverlay" class="modal-overlay"></div>
<div id="changePasswordModal" class="modal-container">
    <div class="modal-header">
        <h2 class="modal-title">🔑 Ganti Password</h2>
        <button class="modal-close" onclick="closeModal('changePassword')">✕</button>
    </div>
    
    <form action="<?= base_url('user/profile/update') ?>" method="POST">
        <?= csrf_field() ?>
        
        <!-- Hidden fields for name, email, phone (keep them same) -->
        <input type="hidden" name="name" value="<?= esc($user['name'] ?? '') ?>">
        <input type="hidden" name="email" value="<?= esc($user['email'] ?? '') ?>">
        <input type="hidden" name="phone" value="<?= esc($user['phone'] ?? '') ?>">
        
        <div class="form-group-clean">
            <label class="form-label-clean">🔑 Password Baru</label>
            <input type="password" name="password" class="form-input-clean" 
                   placeholder="Minimal 6 karakter" required>
        </div>

        <div class="form-group-clean">
            <label class="form-label-clean">✅ Konfirmasi Password Baru</label>
            <input type="password" name="password_confirm" class="form-input-clean" 
                   placeholder="Ketik ulang password baru" required>
        </div>

        <div class="form-actions-clean">
            <button type="submit" class="btn-clean btn-purple">
                💾 Update Password
            </button>
            <button type="button" onclick="closeModal('changePassword')" class="btn-clean btn-dark">
                ✕ Batal
            </button>
        </div>
    </form>
</div>

<?= $this->include('templates/footer') ?>