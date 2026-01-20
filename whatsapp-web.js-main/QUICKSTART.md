# 🚀 Quick Start - OTP Server

## Cara Mudah Jalankan OTP Server

### Windows (Double Click)
1. **Klik 2x** file `start-otp-server.bat`
2. **Scan QR Code** yang muncul dengan WhatsApp
3. **Selesai!** Server siap digunakan

### Manual (Command Line)
```bash
cd C:\laragon\www\project-pkl-eventku\whatsapp-web.js-main
node otp-server.js
```

---

## 🧪 Testing OTP

### Cara 1: Via Browser (Recommended)
1. **Buka** file `test-otp.html` di browser
2. **Masukkan** nama dan nomor HP
3. **Klik** "Kirim OTP ke WhatsApp"
4. **Cek** WhatsApp untuk kode OTP
5. **Input** kode OTP di form
6. **Klik** "Verifikasi OTP"

### Cara 2: Via Aplikasi EventKu
1. Buka `http://localhost/project-pkl-eventku/public/auth/forgot-password`
2. Ikuti flow reset password

---

## ✅ Checklist Setup

- [ ] Node.js terinstall
- [ ] Server OTP berjalan (`start-otp-server.bat`)
- [ ] QR Code sudah di-scan
- [ ] Status WhatsApp: **Connected** ✅
- [ ] Test kirim OTP berhasil

---

## 📱 Status Koneksi

Cek status server:
```
http://localhost:3000/api/status
```

Response jika berhasil:
```json
{
  "success": true,
  "server": "running",
  "whatsapp": "connected",  // ← HARUS "connected"
  "activeOTPs": 0
}
```

---

## 🔧 Troubleshooting Cepat

### QR Code tidak muncul?
```bash
# Hapus session lama
Remove-Item -Recurse -Force .wwebjs_auth
# Restart server
```

### Port 3000 sudah digunakan?
```bash
# Cari process
netstat -ano | findstr :3000
# Kill process (ganti PID)
taskkill /PID <nomor_PID> /F
```

### WhatsApp tidak connect?
1. Scan ulang QR code
2. Pastikan HP terhubung internet
3. Restart server

---

## 📞 Support

Jika masih error, kirim screenshot error + log dari terminal.

**Happy Coding! 🎉**
