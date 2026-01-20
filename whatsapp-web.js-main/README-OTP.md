# 🔐 Panduan OTP WhatsApp - EventKu

## 📋 Status Saat Ini
✅ Server OTP sudah berjalan di `http://localhost:3000`
⏳ **TUNGGU:** QR Code sudah muncul - tinggal scan dengan WhatsApp

---

## 🚀 Langkah-Langkah Aktifkan OTP

### 1. ✅ Start Server (SUDAH JALAN)
Server sudah running di terminal. Jika perlu restart:
```bash
cd C:\laragon\www\project-pkl-eventku\whatsapp-web.js-main
node otp-server.js
```

### 2. 📱 Scan QR Code
**PENTING:** Buka WhatsApp di HP kamu dan scan QR code yang muncul di terminal:
1. Buka WhatsApp di HP
2. Ketuk menu ⋮ (titik tiga) atau ⚙️ Settings
3. Pilih **"Linked Devices"** atau **"Perangkat Tertaut"**
4. Tap **"Link a Device"**
5. Scan QR code yang muncul di terminal

### 3. ⏳ Tunggu Autentikasi
Setelah scan, akan muncul pesan:
```
✅ Authenticated
✅ WhatsApp Client siap!
Sekarang bisa kirim OTP via WhatsApp
```

### 4. ✅ Test OTP
Setelah authenticated, test dengan:

#### A. Via Browser (Test Manual)
Buka file `test-otp.html` yang sudah saya buat

#### B. Via CURL (Test CLI)
```bash
# Test kirim OTP
curl -X POST http://localhost:3000/api/send-otp ^
  -H "Content-Type: application/json" ^
  -d "{\"phoneNumber\":\"08123456789\",\"name\":\"Test User\"}"

# Test verify OTP (ganti dengan kode yang diterima)
curl -X POST http://localhost:3000/api/verify-otp ^
  -H "Content-Type: application/json" ^
  -d "{\"phoneNumber\":\"628123456789\",\"otp\":\"123456\"}"

# Check status server
curl http://localhost:3000/api/status
```

#### C. Via Aplikasi EventKu
1. Buka browser: `http://localhost/project-pkl-eventku/`
2. Pilih **"Lupa Password"**
3. Masukkan nama dan nomor HP
4. Klik **"Kirim Kode OTP"**
5. Cek WhatsApp untuk kode OTP
6. Masukkan kode OTP
7. Reset password

---

## 🔍 Troubleshooting

### ❌ QR Code Terus Muncul / Tidak Terscan
**Solusi:**
- Pastikan WhatsApp sudah terbuka di HP
- Scan ulang QR code yang baru muncul (QR expired setiap 60 detik)
- Pastikan HP terhubung internet yang stabil
- Coba hapus session lama:
  ```bash
  # Stop server (Ctrl+C), lalu:
  Remove-Item -Recurse -Force .wwebjs_auth
  # Jalankan ulang server
  node otp-server.js
  ```

### ❌ Error: ECONNREFUSED
**Penyebab:** Server OTP belum jalan atau port 3000 sudah digunakan

**Solusi:**
```bash
# Cek port 3000
netstat -ano | findstr :3000

# Kill process jika ada
taskkill /PID <PID_NUMBER> /F

# Jalankan ulang server
node otp-server.js
```

### ❌ WhatsApp Disconnected
**Solusi:**
Server akan auto-reconnect dalam 5 detik. Jika tidak:
1. Restart server (Ctrl+C, lalu `node otp-server.js`)
2. Scan QR code lagi

### ❌ OTP Tidak Dikirim
**Cek:**
1. Status WhatsApp Client:
   ```bash
   curl http://localhost:3000/api/status
   ```
   Response harus: `"whatsapp": "connected"`

2. Format nomor HP:
   - ✅ Benar: `08123456789` atau `628123456789`
   - ❌ Salah: `+62 812-3456-789`

3. Nomor WhatsApp harus aktif dan terdaftar

---

## 📊 Status Endpoints

| Endpoint | Method | Status | Fungsi |
|----------|--------|--------|--------|
| `/api/send-otp` | POST | ✅ | Kirim OTP ke WhatsApp |
| `/api/verify-otp` | POST | ✅ | Verifikasi kode OTP |
| `/api/reset-password` | POST | ✅ | Reset password (via OTP server) |
| `/api/status` | GET | ✅ | Cek status server & WhatsApp |

---

## 🎯 Flow Reset Password

```
User Input HP → Send OTP → WhatsApp Message → User Input OTP → Verify → Reset Password
     ↓              ↓              ↓                 ↓            ↓          ↓
  Step 1        API Call    OTP dikirim         OTP input    Validasi   Success!
```

---

## 💡 Tips

1. **Keep Server Running:** Jangan matikan server OTP selama aplikasi digunakan
2. **Session WhatsApp:** Session WhatsApp akan tersimpan di folder `.wwebjs_auth`, tidak perlu scan QR setiap kali
3. **Auto Reconnect:** Server otomatis reconnect jika WhatsApp disconnect
4. **OTP Expiry:** Kode OTP berlaku 5 menit
5. **Resend OTP:** User bisa kirim ulang OTP setelah 60 detik

---

## 🔐 Keamanan

- ✅ OTP disimpan di memory (Map), bukan database
- ✅ Auto-expire setelah 5 menit
- ✅ Auto-delete setelah digunakan
- ✅ Rate limiting (resend cooldown 60 detik)
- ✅ CORS enabled untuk frontend

---

## 📝 Log Monitoring

Monitor terminal untuk melihat:
- ✓ OTP dikirim ke nomor XXX
- ✓ OTP verified untuk nomor XXX
- ✗ Gagal kirim OTP: [error message]

---

**Status:** Server Aktif, Tunggu QR Scan ⏳

**Next Step:** SCAN QR CODE dengan WhatsApp di HP kamu! 📱
