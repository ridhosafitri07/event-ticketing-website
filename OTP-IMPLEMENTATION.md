# 🔐 Implementasi OTP WhatsApp - EventKu

## ✅ Status: BERHASIL DIIMPLEMENTASIKAN!

**Tanggal:** 21 Januari 2026  
**Teknologi:** Python + Selenium + Flask + CodeIgniter 4

---

## 📋 Cara Menggunakan

### 1. **Start Server Python OTP**

```bash
cd C:\laragon\www\project-pkl-eventku\home-ki-whatsappBlast
python app.py
```

**Penting:**
- Browser Chrome WhatsApp Web akan terbuka otomatis
- **JANGAN TUTUP** browser Chrome ini!
- Tunggu ~30 detik sampai WhatsApp login & terkoneksi

---

### 2. **Test Fitur Lupa Password**

#### **Via Browser:**
```
http://localhost/project-pkl-eventku/public/auth/forgotPassword
```

#### **Langkah-langkah:**

**STEP 1: Input Nomor HP**
- Isi **Nama Lengkap** (harus sesuai database)
- Isi **Nomor WhatsApp** (format: 08xxx atau 628xxx)
- Klik **"Kirim Kode OTP"**

**STEP 2: Verifikasi OTP**
- Cek WhatsApp di HP → terima pesan berisi kode OTP 6 digit
- Input kode OTP di halaman web
- Klik **"Verifikasi OTP"**

**STEP 3: Reset Password**
- Masukkan password baru (min 8 karakter)
- Konfirmasi password
- Klik **"Reset Password"**
- ✅ Password berhasil direset!

---

## 🔧 Arsitektur Sistem

```
User Browser (forgot_password.php)
        ↓
CodeIgniter Controller (AuthController.php)
    → sendOTP()     → Hit Python API (localhost:5000/send-otp)
    → verifyOTP()   → Validasi di Session
        ↓
Python Flask Server (app.py)
        ↓
Selenium WhatsApp Web (whatsapp_bot.py)
        ↓
WhatsApp Web → Kirim pesan OTP
```

---

## 📂 File yang Dimodifikasi

### **Backend (CodeIgniter)**
1. `app/Controllers/AuthController.php`
   - ✅ Method `sendOTP()` - Kirim OTP via Python API
   - ✅ Method `verifyOTP()` - Validasi OTP dari session
   - ✅ Method `doResetPassword()` - Update password setelah OTP verified
   - ✅ Helper `formatPhoneNumber()` - Format nomor HP ke 62xxx

2. `app/Config/Routes.php`
   - ✅ Route `POST /auth/sendOTP`
   - ✅ Route `POST /auth/verifyOTP`

### **Frontend**
3. `app/Views/auth/forgot_password.php`
   - ✅ Update JavaScript untuk hit CodeIgniter API
   - ✅ 3-step wizard (Input HP → Verify OTP → Reset Password)
   - ✅ Auto-format nomor HP
   - ✅ OTP timer (5 menit)
   - ✅ Resend OTP timer (60 detik)

### **Python Server**
4. `home-ki-whatsappBlast/app.py`
   - ✅ Flask server di port 5000
   - ✅ CORS enabled
   - ✅ Endpoint `/send-otp` - Kirim OTP via WhatsApp
   - ✅ Endpoint `/blast` - Blast pesan massal (bonus)

5. `home-ki-whatsappBlast/whatsapp_bot.py`
   - ✅ Selenium + ChromeDriver
   - ✅ WhatsApp Web automation
   - ✅ Multi-XPATH fallback (4 variasi)
   - ✅ Auto-retry & error handling

---

## 🎯 Fitur Lengkap

### ✅ Security
- OTP berlaku 5 menit (expired auto)
- OTP tersimpan di session (server-side)
- Validasi nomor HP harus terdaftar di database
- CSRF protection
- Password di-hash dengan bcrypt

### ✅ User Experience
- 3-step wizard dengan indikator progress
- Auto-format nomor HP (08xxx → 628xxx)
- OTP input auto-focus (6 digit boxes)
- Copy-paste OTP support
- Countdown timer OTP & resend
- Toast notifications (success/error)
- Loading states pada button

### ✅ Reliability
- Multi-XPATH fallback untuk WhatsApp input box
- Error handling di semua layer
- Timeout protection (45 detik wait)
- Auto-reconnect WhatsApp Web
- Detailed error messages

---

## 🚀 Quick Start

### **Setup Awal (Sekali Saja)**

1. Install Python dependencies:
```bash
cd C:\laragon\www\project-pkl-eventku\home-ki-whatsappBlast
pip install flask flask-cors selenium webdriver_manager
```

2. Download ChromeDriver (sudah dilakukan):
```
C:\chromedrive\chromedriver.exe
```

---

### **Setiap Kali Mau Jalankan**

1. **Start Python Server:**
```bash
cd C:\laragon\www\project-pkl-eventku\home-ki-whatsappBlast
python app.py
```

2. **Scan QR WhatsApp (Pertama Kali Saja):**
   - Browser Chrome akan terbuka otomatis
   - Scan QR code dengan WhatsApp di HP
   - Tunggu sampai terkoneksi
   - **JANGAN TUTUP browser Chrome ini!**

3. **Start Laragon** (Apache + MySQL)

4. **Akses Aplikasi:**
```
http://localhost/project-pkl-eventku/public/
```

---

## 📝 API Endpoints

### **CodeIgniter Endpoints**

#### 1. Send OTP
```
POST /auth/sendOTP
Content-Type: application/json

Request:
{
  "name": "Ega Yaro Setyawan",
  "phone": "08994523928"
}

Response (Success):
{
  "status": "success",
  "message": "OTP berhasil dikirim ke WhatsApp",
  "phone": "628994523928"
}

Response (Error):
{
  "status": "error",
  "message": "Nomor HP tidak terdaftar"
}
```

#### 2. Verify OTP
```
POST /auth/verifyOTP
Content-Type: application/json

Request:
{
  "phone": "628994523928",
  "otp": "123456"
}

Response (Success):
{
  "status": "success",
  "message": "OTP berhasil diverifikasi",
  "phone": "628994523928"
}

Response (Error):
{
  "status": "error",
  "message": "Kode OTP salah"
}
```

---

### **Python Endpoints**

#### 1. Send OTP via WhatsApp
```
POST http://localhost:5000/send-otp
Content-Type: application/json

Request:
{
  "phone": "628994523928",
  "name": "Ega Yaro Setyawan"
}

Response:
{
  "status": "success",
  "otp": "123456"
}
```

#### 2. WhatsApp Blast (Bonus Feature)
```
POST http://localhost:5000/blast
Content-Type: application/json

Request:
{
  "phone": "628994523928",
  "message": "Hello from EventKu!"
}

Response:
{
  "status": "success"
}
```

---

## 🐛 Troubleshooting

### ❌ Error: "Failed to fetch"
**Penyebab:** Server Python tidak jalan

**Solusi:**
```bash
cd C:\laragon\www\project-pkl-eventku\home-ki-whatsappBlast
python app.py
```

---

### ❌ OTP tidak terkirim / "ngambang"
**Penyebab:** WhatsApp Web belum terkoneksi atau XPATH berubah

**Solusi:**
1. Pastikan browser Chrome WhatsApp Web masih terbuka
2. Pastikan WhatsApp sudah login & terkoneksi
3. Cek terminal Python untuk log error
4. Script sudah pakai 4 XPATH fallback (seharusnya aman)

---

### ❌ Error: "Nomor HP tidak terdaftar"
**Penyebab:** Nomor HP belum ada di database `users` table

**Solusi:**
1. Register dulu di halaman register
2. ATAU manual insert ke database dengan nomor HP yang sama

---

### ❌ Browser Chrome crash
**Penyebab:** ChromeDriver issue atau memory

**Solusi:**
```bash
# Kill semua proses
Get-Process python,chrome,chromedriver -ErrorAction SilentlyContinue | Stop-Process -Force

# Restart server
cd C:\laragon\www\project-pkl-eventku\home-ki-whatsappBlast
python app.py
```

---

## 📊 Testing Checklist

- [x] Send OTP ke nomor terdaftar → ✅ Berhasil
- [x] Send OTP ke nomor tidak terdaftar → ❌ Error (expected)
- [x] Verify OTP dengan kode benar → ✅ Berhasil
- [x] Verify OTP dengan kode salah → ❌ Error (expected)
- [x] OTP expired setelah 5 menit → ❌ Error (expected)
- [x] Resend OTP setelah 60 detik → ✅ Berhasil
- [x] Reset password → ✅ Berhasil
- [x] Login dengan password baru → ✅ Berhasil

---

## 🎉 Selesai!

Fitur OTP WhatsApp untuk reset password sudah **100% berfungsi**!

**Developer:** GitHub Copilot & Ega Yaro Setyawan  
**Date:** 21 Januari 2026
