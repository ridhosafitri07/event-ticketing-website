@echo off
title OTP WhatsApp Server - EventKu
color 0A

echo.
echo ============================================
echo    OTP WhatsApp Server - EventKu
echo ============================================
echo.

cd /d C:\laragon\www\project-pkl-eventku\whatsapp-web.js-main

echo [1/3] Memeriksa Node.js...
where node >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Node.js tidak ditemukan!
    echo Silakan install Node.js terlebih dahulu.
    pause
    exit /b 1
)

echo [OK] Node.js ditemukan
node --version
echo.

echo [2/3] Memeriksa dependencies...
if not exist "node_modules" (
    echo [WARNING] Node modules belum terinstall
    echo [INFO] Menginstall dependencies...
    call npm install
    if %errorlevel% neq 0 (
        echo [ERROR] Gagal install dependencies
        pause
        exit /b 1
    )
)
echo [OK] Dependencies siap
echo.

echo [3/3] Memulai server OTP...
echo.
echo ============================================
echo  Server akan berjalan di http://localhost:3000
echo  
echo  PENTING: 
echo  1. SCAN QR CODE yang muncul dengan WhatsApp
echo  2. Buka test-otp.html untuk testing
echo  3. Tekan Ctrl+C untuk stop server
echo ============================================
echo.

node otp-server.js

if %errorlevel% neq 0 (
    echo.
    echo [ERROR] Server gagal berjalan!
    echo Periksa apakah port 3000 sudah digunakan.
    pause
    exit /b 1
)
