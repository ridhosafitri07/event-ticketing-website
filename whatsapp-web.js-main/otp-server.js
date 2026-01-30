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
            '--disable-setuid-sandbox'
        ]
    }
});
// Storage untuk OTP (production: pakai database)
const otpStorage = new Map();

// Flag untuk status WhatsApp
let isWhatsAppReady = false;

// WhatsApp Events dengan error handling
whatsappClient.on('qr', (qr) => {
    console.log('\n=== SCAN QR CODE INI ===');
    qrcode.generate(qr, { small: true });
    console.log('Scan QR code dengan WhatsApp di HP kamu\n');
});

whatsappClient.on('ready', () => {
    isWhatsAppReady = true;
    console.log('\n✅ WhatsApp Client siap!');
    console.log('Sekarang bisa kirim OTP via WhatsApp');
});

whatsappClient.on('authenticated', () => {
    console.log('✅ Authenticated');
    
    // WORKAROUND: Set ready setelah 5 detik dari authenticated
    // Karena event "ready" kadang tidak fire di versi tertentu
    setTimeout(() => {
        if (!isWhatsAppReady) {
            console.log('⚠️ Event "ready" tidak fire, menggunakan workaround...');
            isWhatsAppReady = true;
            console.log('✅ WhatsApp Client siap! (via workaround)');
            console.log('Sekarang bisa kirim OTP via WhatsApp');
        }
    }, 5000);
});

whatsappClient.on('loading_screen', (percent, message) => {
    console.log('Loading...', percent, message);
});

whatsappClient.on('change_state', state => {
    console.log('State changed:', state);
});

whatsappClient.on('auth_failure', (msg) => {
    isWhatsAppReady = false;
    console.error('❌ Authentication gagal:', msg);
});

whatsappClient.on('disconnected', (reason) => {
    isWhatsAppReady = false;
    console.log('⚠️ Client disconnected:', reason);
    console.log('⚠️ Silakan restart server secara manual');
    // Tidak auto-reconnect untuk avoid conflict
});

// Initialize dengan retry
let retryCount = 0;
const maxRetries = 3;

async function initializeClient() {
    try {
        console.log('Menginisialisasi WhatsApp Client...');
        await whatsappClient.initialize();
        retryCount = 0; // Reset counter jika berhasil
    } catch (error) {
        console.error('❌ Error saat initialize:', error.message);
        
        if (retryCount < maxRetries) {
            retryCount++;
            console.log(`Mencoba lagi (${retryCount}/${maxRetries}) dalam 10 detik...`);
            setTimeout(initializeClient, 10000);
        } else {
            console.error('❌ Gagal initialize setelah beberapa percobaan');
            console.error('💡 Solusi: Tutup semua Chrome, hapus folder .wwebjs_auth, lalu restart server');
            process.exit(1); // Exit agar tidak loop terus
        }
    }
}

// ===== API ENDPOINTS =====

// 1. Generate dan kirim OTP
app.post('/api/send-otp', async (req, res) => {
    try {
        // Cek apakah WhatsApp sudah ready
        if (!isWhatsAppReady) {
            return res.status(503).json({
                status: 'error',
                success: false,
                message: 'WhatsApp belum siap. Silakan tunggu beberapa saat dan coba lagi.'
            });
        }

        const { phoneNumber, name } = req.body;

        if (!phoneNumber) {
            return res.status(400).json({
                status: 'error',
                success: false,
                message: 'Nomor telepon harus diisi'
            });
        }

        // Generate OTP 6 digit
        const otp = Math.floor(100000 + Math.random() * 900000).toString();
        
        // Format nomor WhatsApp - coba beberapa format
        let chatId = phoneNumber.replace(/[^0-9]/g, '');
        
        // Pastikan format 62xxx
        if (chatId.startsWith('0')) {
            chatId = '62' + chatId.substring(1);
        }
        
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

        // Kirim pesan WhatsApp dengan timeout yang lebih panjang
        try {
            // Tunggu sebentar untuk stabilitas
            await new Promise(resolve => setTimeout(resolve, 2000));
            
            // Coba dengan getNumberId terlebih dahulu
            const numberId = await whatsappClient.getNumberId(chatId);
            
            if (!numberId) {
                otpStorage.delete(phoneNumber);
                return res.status(400).json({
                    status: 'error',
                    success: false,
                    message: 'Nomor WhatsApp tidak terdaftar atau tidak valid.'
                });
            }
            
            console.log(`Mencoba kirim ke ${numberId._serialized}...`);
            
            // FIX: Kirim dengan { sendSeen: false } untuk avoid error markedUnread
            const sentMessage = await whatsappClient.sendMessage(numberId._serialized, message, { sendSeen: false });
            
            if (sentMessage) {
                console.log(`✓ OTP berhasil dikirim ke ${phoneNumber}: ${otp}`);
                
                return res.json({
                    status: 'success',
                    success: true,
                    message: 'Kode OTP berhasil dikirim ke WhatsApp Anda',
                    otp: otp,
                    expiresIn: 300
                });
            }
            
        } catch (error) {
            console.error(`✗ Error mengirim OTP ke ${phoneNumber}:`, error.message);
            otpStorage.delete(phoneNumber);
            
            return res.status(500).json({
                status: 'error',  // Tambahkan status
                success: false,
                message: 'Timeout mengirim OTP. WhatsApp Web sedang lambat, silakan coba lagi.',
                error: error.message
            });
        }

        // Auto-cleanup OTP setelah 5 menit
        setTimeout(() => {
            otpStorage.delete(phoneNumber);
        }, 5 * 60 * 1000);

    } catch (error) {
        console.error('Error mengirim OTP:', error);
        res.status(500).json({
            status: 'error',  // Tambahkan status
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
    res.json({
        success: true,
        server: 'running',
        whatsapp: isWhatsAppReady ? 'ready' : 'not_ready',
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