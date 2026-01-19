const express = require('express');
const cors = require('cors');
const qrcode = require('qrcode-terminal');
const { Client, LocalAuth } = require('whatsapp-web.js');


const app = express();
const PORT = 3000;

// Middleware
app.use(cors());
app.use(express.json());

// Initialize WhatsApp Client
const whatsappClient = new Client({
    authStrategy: new LocalAuth(),
    puppeteer: {
        headless: false,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--disable-gpu',
            '--disable-extensions',
            '--disable-software-rasterizer',
            '--disable-web-security',
            '--disable-features=IsolateOrigins',
            '--disable-site-isolation-trials'
        ],
        timeout: 60000
    },
    webVersionCache: {
        type: 'remote',
        remotePath: 'https://raw.githubusercontent.com/wppconnect-team/wa-version/main/html/2.2412.54.html'
    }
});
// Storage untuk OTP (production: pakai database)
const otpStorage = new Map();

// WhatsApp Events dengan error handling
whatsappClient.on('qr', (qr) => {
    console.log('\n=== SCAN QR CODE INI ===');
    qrcode.generate(qr, { small: true });
    console.log('Scan QR code dengan WhatsApp di HP kamu\n');
});

whatsappClient.on('ready', () => {
    console.log('\n✅ WhatsApp Client siap!');
    console.log('Sekarang bisa kirim OTP via WhatsApp');
});

whatsappClient.on('authenticated', () => {
    console.log('✅ Authenticated');
});

whatsappClient.on('auth_failure', (msg) => {
    console.error('❌ Authentication gagal:', msg);
});

whatsappClient.on('disconnected', (reason) => {
    console.log('⚠️ Client disconnected:', reason);
    console.log('Mencoba reconnect...');
    setTimeout(() => {
        initializeClient();
    }, 5000);
});

// Initialize dengan retry
let retryCount = 0;
const maxRetries = 3;

async function initializeClient() {
    try {
        console.log('Menginisialisasi WhatsApp Client...');
        await whatsappClient.initialize();
    } catch (error) {
        console.error('❌ Error saat initialize:', error.message);
        
        if (retryCount < maxRetries) {
            retryCount++;
            console.log(`Mencoba lagi (${retryCount}/${maxRetries})...`);
            setTimeout(initializeClient, 5000);
        } else {
            console.error('Gagal initialize setelah beberapa percobaan');
        }
    }
}

// ===== API ENDPOINTS =====

// 1. Generate dan kirim OTP
app.post('/api/send-otp', async (req, res) => {
    try {
        const { phoneNumber, name } = req.body;

        if (!phoneNumber) {
            return res.status(400).json({
                success: false,
                message: 'Nomor telepon harus diisi'
            });
        }

        // Generate OTP 6 digit
        const otp = Math.floor(100000 + Math.random() * 900000).toString();
        
        // Format nomor WhatsApp
        const chatId = phoneNumber.replace(/[^0-9]/g, '') + '@c.us';
        
        // Simpan OTP dengan expiry 5 menit
        const expiryTime = Date.now() + (5 * 60 * 1000);
        otpStorage.set(phoneNumber, {
            otp: otp,
            expiry: expiryTime,
            verified: false
        });

        // Template pesan OTP
        const message = `Halo ${name || 'User'}! 👋

Kami menerima permintaan *reset password* untuk akun Anda.

🔐 *${otp}*

Kode ini *berlaku selama 5 menit*.
⚠️ Demi keamanan, jangan bagikan kode ini kepada siapa pun, termasuk pihak kami.

Salam,
Tim Web Ticketing`;

        // Kirim response dulu
        res.json({
            success: true,
            message: 'Kode OTP berhasil dikirim ke WhatsApp Anda',
            expiresIn: 300
        });

        // Kirim pesan WhatsApp di background
        whatsappClient.sendMessage(chatId, message)
            .then(() => {
                console.log(`✓ OTP dikirim ke ${phoneNumber}: ${otp}`);
            })
            .catch((error) => {
                console.error(`✗ Gagal kirim OTP ke ${phoneNumber}:`, error.message);
            });

        // Auto-cleanup OTP setelah 5 menit
        setTimeout(() => {
            otpStorage.delete(phoneNumber);
        }, 5 * 60 * 1000);

    } catch (error) {
        console.error('Error mengirim OTP:', error);
        res.status(500).json({
            success: false,
            message: 'Gagal mengirim OTP. Pastikan nomor WhatsApp aktif.',
            error: error.message
        });
    }
});

// 2. Verifikasi OTP
app.post('/api/verify-otp', (req, res) => {
    try {
        const { phoneNumber, otp } = req.body;

        if (!phoneNumber || !otp) {
            return res.status(400).json({
                success: false,
                message: 'Nomor telepon dan OTP harus diisi'
            });
        }

        const storedOtp = otpStorage.get(phoneNumber);

        if (!storedOtp) {
            return res.status(400).json({
                success: false,
                message: 'Kode OTP tidak ditemukan atau sudah expired'
            });
        }

        if (Date.now() > storedOtp.expiry) {
            otpStorage.delete(phoneNumber);
            return res.status(400).json({
                success: false,
                message: 'Kode OTP sudah kadaluarsa. Silakan minta kode baru.'
            });
        }

        if (storedOtp.otp !== otp) {
            return res.status(400).json({
                success: false,
                message: 'Kode OTP salah'
            });
        }

        storedOtp.verified = true;
        otpStorage.set(phoneNumber, storedOtp);

        console.log(`✓ OTP verified untuk ${phoneNumber}`);

        res.json({
            success: true,
            message: 'Kode OTP berhasil diverifikasi',
            verified: true
        });

    } catch (error) {
        console.error('Error verifikasi OTP:', error);
        res.status(500).json({
            success: false,
            message: 'Terjadi kesalahan saat verifikasi OTP',
            error: error.message
        });
    }
});

// 3. Reset Password
app.post('/api/reset-password', (req, res) => {
    try {
        const { phoneNumber, newPassword } = req.body;

        if (!phoneNumber || !newPassword) {
            return res.status(400).json({
                success: false,
                message: 'Nomor telepon dan password baru harus diisi'
            });
        }

        const storedOtp = otpStorage.get(phoneNumber);

        if (!storedOtp || !storedOtp.verified) {
            return res.status(400).json({
                success: false,
                message: 'Silakan verifikasi OTP terlebih dahulu'
            });
        }

        otpStorage.delete(phoneNumber);

        console.log(`✓ Password reset untuk ${phoneNumber}`);

        res.json({
            success: true,
            message: 'Password berhasil direset'
        });

    } catch (error) {
        console.error('Error reset password:', error);
        res.status(500).json({
            success: false,
            message: 'Gagal reset password',
            error: error.message
        });
    }
});

// 4. Check server status
app.get('/api/status', (req, res) => {
    const isWhatsAppReady = whatsappClient.info !== null;
    
    res.json({
        success: true,
        server: 'running',
        whatsapp: isWhatsAppReady ? 'connected' : 'disconnected',
        activeOTPs: otpStorage.size
    });
});

// Start server dan WhatsApp client
app.listen(PORT, () => {
    console.log(`\n=== OTP SERVER ===`);
    console.log(`Server berjalan di http://localhost:${PORT}`);
    console.log(`\nEndpoints:`);
    console.log(`POST /api/send-otp      - Kirim OTP`);
    console.log(`POST /api/verify-otp    - Verifikasi OTP`);
    console.log(`POST /api/reset-password - Reset password`);
    console.log(`GET  /api/status        - Cek status server\n`);
});

// Initialize WhatsApp Client
initializeClient();