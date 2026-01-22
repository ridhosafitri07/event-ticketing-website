<?= $this->include('templates/header') ?>

<div class="particle"></div>
<div class="particle"></div>
<div class="particle"></div>

<div class="auth-container forgot-password-container" style="position: relative; z-index: 1;">
    <div class="auth-header">
        <div class="auth-icon">🔐</div>
        <h1>Lupa Password</h1>
        <p id="step-description">Masukkan nomor WhatsApp untuk reset password</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">✓ <?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">✗ <?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="step-indicators">
        <div class="step-indicator active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-label">Nomor HP</div>
        </div>
        <div class="step-line"></div>
        <div class="step-indicator" data-step="2">
            <div class="step-number">2</div>
            <div class="step-label">Verifikasi OTP</div>
        </div>
        <div class="step-line"></div>
        <div class="step-indicator" data-step="3">
            <div class="step-number">3</div>
            <div class="step-label">Password Baru</div>
        </div>
    </div>

    <!-- STEP 1: Input Nomor WhatsApp -->
    <div class="step-content active" id="step-1">
        <form id="form-send-otp" class="auth-form">
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" placeholder="Nama kamu" required>
            </div>

            <div class="form-group">
                <label for="phone">Nomor WhatsApp</label>
                <input type="tel" id="phone" name="phone" placeholder="08xxxxxxxxxx" required>
                <small class="form-hint">Gunakan nomor yang terdaftar di akun kamu</small>
            </div>

            <button type="submit" class="btn btn-primary btn-block" id="btn-send-otp">
                <span class="btn-text">Kirim Kode OTP</span>
                <span class="btn-loading" style="display: none;">
                    <span class="spinner"></span> Mengirim...
                </span>
            </button>
        </form>
    </div>

    <!-- STEP 2: Input OTP -->
    <div class="step-content" id="step-2">
        <div class="otp-info">
            <p id="otp-status">⏳ OTP sedang dikirim ke WhatsApp <strong id="phone-display"></strong>...</p>
            <p class="otp-note" style="color: #94a3b8; font-size: 13px; margin-top: 8px;">
                Mohon tunggu 5-10 detik untuk pesan sampai
            </p>
            <p class="otp-timer">Berlaku selama: <span id="timer">05:00</span></p>
        </div>

        <form id="form-verify-otp" class="auth-form">
            <div class="form-group">
                <label>Masukkan Kode OTP (6 digit)</label>
                <div class="otp-input-group">
                    <input type="text" class="otp-input" maxlength="1" data-index="0" autofocus>
                    <input type="text" class="otp-input" maxlength="1" data-index="1">
                    <input type="text" class="otp-input" maxlength="1" data-index="2">
                    <input type="text" class="otp-input" maxlength="1" data-index="3">
                    <input type="text" class="otp-input" maxlength="1" data-index="4">
                    <input type="text" class="otp-input" maxlength="1" data-index="5">
                </div>
                <small class="form-hint otp-error" style="color: #ef4444; display: none;">Kode OTP salah</small>
            </div>

            <button type="submit" class="btn btn-primary btn-block" id="btn-verify-otp">
                <span class="btn-text">Verifikasi OTP</span>
                <span class="btn-loading" style="display: none;">
                    <span class="spinner"></span> Memverifikasi...
                </span>
            </button>

            <button type="button" class="btn btn-outline btn-block" id="btn-resend-otp" disabled>
                Kirim Ulang OTP <span id="resend-timer">(60s)</span>
            </button>
        </form>
    </div>

    <!-- STEP 3: Reset Password -->
    <div class="step-content" id="step-3">
        <form id="form-reset-password" action="<?= base_url('auth/doResetPassword') ?>" method="POST" class="auth-form">
            <?= csrf_field() ?>
            <input type="hidden" name="phone" id="phone-verified">

            <div class="form-group">
                <label for="new_password">Password Baru</label>
                <input type="password" id="new_password" name="new_password" placeholder="Minimal 8 karakter" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password baru" required>
                <small class="form-hint password-error" style="color: #ef4444; display: none;">Password tidak sama</small>
            </div>

            <button type="submit" class="btn btn-primary btn-block" id="btn-reset-password">
                <span class="btn-text">Reset Password</span>
                <span class="btn-loading" style="display: none;">
                    <span class="spinner"></span> Memproses...
                </span>
            </button>
        </form>
    </div>

    <div class="auth-footer">
        <p><a href="<?= base_url('auth/login') ?>" class="link-primary">← Kembali ke Login</a></p>
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
    overflow-x: hidden;
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
}

.particle:nth-child(1) {
    top: 20%;
    left: 20%;
    animation: float 8s ease-in-out infinite;
}

.particle:nth-child(2) {
    top: 60%;
    right: 30%;
    animation: float 10s ease-in-out infinite 2s;
}

.particle:nth-child(3) {
    bottom: 30%;
    left: 40%;
    animation: float 12s ease-in-out infinite 4s;
}

@keyframes float {
    0%, 100% { transform: translateY(0) scale(1); opacity: 0.3; }
    50% { transform: translateY(-30px) scale(1.5); opacity: 0.7; }
}

.forgot-password-container {
    max-width: 500px;
}

/* Step Indicators */
.step-indicators {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 32px;
    gap: 8px;
}

.step-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    opacity: 0.4;
    transition: all 0.3s ease;
}

.step-indicator.active {
    opacity: 1;
}

.step-indicator.completed {
    opacity: 0.8;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    transition: all 0.3s ease;
}

.step-indicator.active .step-number {
    background: linear-gradient(135deg, #7C3AED, #EC4899);
    border-color: transparent;
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.4);
}

.step-indicator.completed .step-number {
    background: rgba(16, 185, 129, 0.2);
    border-color: #10b981;
}

.step-label {
    font-size: 12px;
    font-weight: 600;
    color: #94a3b8;
    text-align: center;
}

.step-indicator.active .step-label {
    color: #f8fafc;
}

.step-line {
    width: 40px;
    height: 2px;
    background: rgba(255, 255, 255, 0.1);
}

/* Step Content */
.step-content {
    display: none;
}

.step-content.active {
    display: block;
    animation: fadeInUp 0.4s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* OTP Input */
.otp-info {
    text-align: center;
    margin-bottom: 24px;
    padding: 16px;
    background: rgba(124, 58, 237, 0.1);
    border-radius: 12px;
    border: 1px solid rgba(124, 58, 237, 0.2);
}

.otp-info p {
    margin: 4px 0;
    font-size: 14px;
}

.otp-timer {
    color: #EC4899;
    font-weight: 600;
}

.otp-input-group {
    display: flex;
    gap: 12px;
    justify-content: center;
    margin: 20px 0;
}

.otp-input {
    width: 50px;
    height: 60px;
    text-align: center;
    font-size: 24px;
    font-weight: 700;
    border: 2px solid rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.05);
    color: #f8fafc;
    transition: all 0.2s ease;
}

.otp-input:focus {
    outline: none;
    border-color: #7C3AED;
    background: rgba(124, 58, 237, 0.1);
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
}

.otp-input.filled {
    border-color: #10b981;
    background: rgba(16, 185, 129, 0.1);
}

.otp-input.error {
    border-color: #ef4444;
    background: rgba(239, 68, 68, 0.1);
    animation: shake 0.4s ease;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* Button Loading */
.btn-loading {
    display: none;
}

.btn.loading .btn-text {
    display: none;
}

.btn.loading .btn-loading {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Button Outline */
.btn-outline {
    background: transparent;
    border: 2px solid rgba(255, 255, 255, 0.15);
    color: #f8fafc;
}

.btn-outline:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.25);
}

.btn-outline:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Responsive */
@media (max-width: 480px) {
    .step-indicators {
        gap: 4px;
    }
    
    .step-label {
        font-size: 10px;
    }
    
    .step-line {
        width: 20px;
    }
    
    .otp-input {
        width: 42px;
        height: 52px;
        font-size: 20px;
    }
    
    .otp-input-group {
        gap: 8px;
    }
}
</style>

<script>
const BASE_URL = '<?= base_url() ?>';
let currentStep = 1;
let phoneNumber = '';
let userName = '';
let otpTimer = null;
let resendTimer = null;

// Step Navigation
function goToStep(step) {
    // Update step indicators
    document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
        indicator.classList.remove('active', 'completed');
        if (index + 1 < step) {
            indicator.classList.add('completed');
        } else if (index + 1 === step) {
            indicator.classList.add('active');
        }
    });

    // Update step content
    document.querySelectorAll('.step-content').forEach((content, index) => {
        content.classList.remove('active');
        if (index + 1 === step) {
            content.classList.add('active');
        }
    });

    // Update description
    const descriptions = {
        1: 'Masukkan nomor WhatsApp untuk reset password',
        2: 'Masukkan kode OTP yang dikirim ke WhatsApp',
        3: 'Buat password baru untuk akun kamu'
    };
    document.getElementById('step-description').textContent = descriptions[step];

    currentStep = step;
}

// STEP 1: Send OTP
document.getElementById('form-send-otp').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const btn = document.getElementById('btn-send-otp');
    btn.classList.add('loading');
    btn.disabled = true;

    userName = document.getElementById('name').value.trim();
    phoneNumber = document.getElementById('phone').value.trim();

    // Format nomor (remove spaces, dashes)
    phoneNumber = phoneNumber.replace(/[\s\-]/g, '');

    // Convert 08xx to 628xx
    if (phoneNumber.startsWith('08')) {
        phoneNumber = '62' + phoneNumber.substring(1);
    }

    try {
        const response = await fetch(`${BASE_URL}/auth/sendOTP`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ phone: phoneNumber, name: userName })
        });

        const result = await response.json();
        
        // Debug log
        console.log('📤 Send OTP Response:', result);
        if (result.debug_otp) {
            console.log('🔐 DEBUG - OTP CODE:', result.debug_otp);
        }

        if (result.status === 'success') {
            // Show success toast
            showToast('success', 'OTP sedang dikirim ke WhatsApp!');
            
            // Go to step 2 - OTP dikirim di background
            document.getElementById('phone-display').textContent = result.phone || phoneNumber;
            phoneNumber = result.phone || phoneNumber;
            goToStep(2);
            startOtpTimer();
            startResendTimer();
            
            // Update UI setelah 8 detik (estimasi WhatsApp terkirim)
            setTimeout(() => {
                const otpStatus = document.getElementById('otp-status');
                if (otpStatus) {
                    otpStatus.innerHTML = '✅ Kode OTP telah dikirim ke WhatsApp <strong>' + (result.phone || phoneNumber) + '</strong>';
                }
                const otpNote = document.querySelector('.otp-note');
                if (otpNote) {
                    otpNote.remove();
                }
            }, 8000);
        } else {
            showToast('error', result.message || 'Gagal mengirim OTP');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Gagal mengirim OTP. Pastikan server WhatsApp berjalan!');
    } finally {
        btn.classList.remove('loading');
        btn.disabled = false;
    }
});

// OTP Timer (5 minutes)
function startOtpTimer() {
    let seconds = 300; // 5 minutes
    const timerEl = document.getElementById('timer');
    
    otpTimer = setInterval(() => {
        seconds--;
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        timerEl.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        
        if (seconds <= 0) {
            clearInterval(otpTimer);
            timerEl.textContent = 'Expired';
            timerEl.style.color = '#ef4444';
        }
    }, 1000);
}

// Resend Timer (60 seconds)
function startResendTimer() {
    let seconds = 60;
    const btn = document.getElementById('btn-resend-otp');
    const timer = document.getElementById('resend-timer');
    
    btn.disabled = true;
    
    resendTimer = setInterval(() => {
        seconds--;
        timer.textContent = `(${seconds}s)`;
        
        if (seconds <= 0) {
            clearInterval(resendTimer);
            btn.disabled = false;
            timer.textContent = '';
        }
    }, 1000);
}

// Resend OTP
document.getElementById('btn-resend-otp').addEventListener('click', async () => {
    const btn = document.getElementById('btn-resend-otp');
    btn.disabled = true;

    try {
        const response = await fetch(`${BASE_URL}/auth/sendOTP`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ phone: phoneNumber, name: userName })
        });

        const result = await response.json();

        if (result.status === 'success') {
            showToast('success', 'OTP baru telah dikirim!');
            clearInterval(otpTimer);
            startOtpTimer();
            startResendTimer();
            
            // Clear OTP inputs
            document.querySelectorAll('.otp-input').forEach(input => {
                input.value = '';
                input.classList.remove('filled', 'error');
            });
        } else {
            showToast('error', result.message || 'Gagal mengirim ulang OTP');
        }
    } catch (error) {
        showToast('error', 'Gagal mengirim ulang OTP');
    }
});

// OTP Input Auto-focus
const otpInputs = document.querySelectorAll('.otp-input');

otpInputs.forEach((input, index) => {
    input.addEventListener('input', (e) => {
        const value = e.target.value;
        
        if (value.length === 1) {
            input.classList.add('filled');
            input.classList.remove('error');
            
            // Auto-focus next input
            if (index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        } else {
            input.classList.remove('filled');
        }
    });

    input.addEventListener('keydown', (e) => {
        // Backspace: go to previous input
        if (e.key === 'Backspace' && !input.value && index > 0) {
            otpInputs[index - 1].focus();
        }
        
        // Only allow numbers
        if (!/^[0-9]$/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
            e.preventDefault();
        }
    });

    input.addEventListener('paste', (e) => {
        e.preventDefault();
        const pasteData = e.clipboardData.getData('text').replace(/\D/g, '').substring(0, 6);
        
        pasteData.split('').forEach((char, i) => {
            if (otpInputs[i]) {
                otpInputs[i].value = char;
                otpInputs[i].classList.add('filled');
            }
        });
        
        if (pasteData.length === 6) {
            otpInputs[5].focus();
        }
    });
});

// STEP 2: Verify OTP
document.getElementById('form-verify-otp').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const otp = Array.from(otpInputs).map(input => input.value).join('');
    
    if (otp.length !== 6) {
        showToast('error', 'Masukkan 6 digit OTP');
        return;
    }

    const btn = document.getElementById('btn-verify-otp');
    btn.classList.add('loading');
    btn.disabled = true;

    try {
        const response = await fetch(`${BASE_URL}/auth/verifyOTP`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ phone: phoneNumber, otp: otp })
        });

        const result = await response.json();
        
        // Debug log
        console.log('🔍 Verify OTP Request:', { phone: phoneNumber, otp: otp });
        console.log('🔍 Verify OTP Response:', result);

        if (result.status === 'success') {
            showToast('success', 'OTP berhasil diverifikasi!');
            clearInterval(otpTimer);
            clearInterval(resendTimer);
            
            // Go to step 3
            document.getElementById('phone-verified').value = result.phone || phoneNumber;
            goToStep(3);
        } else {
            showToast('error', result.message || 'Kode OTP salah');
            
            // Mark inputs as error
            otpInputs.forEach(input => {
                input.classList.add('error');
                input.classList.remove('filled');
            });
            
            // Clear and focus first input
            setTimeout(() => {
                otpInputs.forEach(input => {
                    input.value = '';
                    input.classList.remove('error');
                });
                otpInputs[0].focus();
            }, 400);
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Gagal memverifikasi OTP');
    } finally {
        btn.classList.remove('loading');
        btn.disabled = false;
    }
});

// STEP 3: Reset Password Validation
document.getElementById('form-reset-password').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const errorEl = document.querySelector('.password-error');

    if (newPassword !== confirmPassword) {
        e.preventDefault();
        errorEl.style.display = 'block';
        document.getElementById('confirm_password').style.borderColor = '#ef4444';
        return false;
    }

    if (newPassword.length < 8) {
        e.preventDefault();
        errorEl.textContent = 'Password minimal 8 karakter';
        errorEl.style.display = 'block';
        document.getElementById('new_password').style.borderColor = '#ef4444';
        return false;
    }

    document.getElementById('btn-reset-password').classList.add('loading');
    document.getElementById('btn-reset-password').disabled = true;
});

document.getElementById('confirm_password').addEventListener('input', function() {
    document.querySelector('.password-error').style.display = 'none';
    this.style.borderColor = '';
});

document.getElementById('new_password').addEventListener('input', function() {
    document.querySelector('.password-error').style.display = 'none';
    this.style.borderColor = '';
});

// Toast Notification
function showToast(type, message) {
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) existingToast.remove();

    const toast = document.createElement('div');
    toast.className = `toast-notification ${type}`;
    toast.innerHTML = `
        <div class="toast-icon">${type === 'success' ? '✓' : '✗'}</div>
        <div class="toast-content">
            <div class="toast-title">${type === 'success' ? 'Berhasil' : 'Gagal'}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">×</button>
    `;
    
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}
</script>

<?= $this->endSection() ?>

<?= $this->include('templates/footer') ?>
