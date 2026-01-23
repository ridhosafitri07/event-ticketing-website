# SETUP AUTO-CANCEL EXPIRED BOOKINGS

## Windows (Laragon / XAMPP)

### Menggunakan Task Scheduler Windows

1. Buka **Task Scheduler** (Windows + R, ketik `taskschd.msc`)

2. Klik **Create Basic Task**

3. Isi detail:
   - **Name**: EventKu Auto Cancel Expired Bookings
   - **Description**: Otomatis cancel booking yang melewati deadline pembayaran
   - **Trigger**: Daily (atau setiap jam jika butuh lebih sering)
   - **Time**: Pilih waktu (misalnya setiap jam: 00:00, 01:00, dst)

4. **Action**: Start a Program
   - **Program/script**: `C:\laragon\bin\php\php8.3.1\php.exe` (sesuaikan path PHP Anda)
   - **Add arguments**: `spark booking:cancel-expired`
   - **Start in**: `C:\laragon\www\project-pkl-eventku` (path project Anda)

5. Finish dan Test

### Test Manual
```bash
cd C:\laragon\www\project-pkl-eventku
php spark booking:cancel-expired
```

## Linux / Ubuntu

### Menggunakan Cron Job

1. Edit crontab:
```bash
crontab -e
```

2. Tambahkan baris ini (jalankan setiap 5 menit):
```bash
*/5 * * * * cd /var/www/project-pkl-eventku && php spark booking:cancel-expired >> /var/log/eventku-cron.log 2>&1
```

3. Atau setiap jam:
```bash
0 * * * * cd /var/www/project-pkl-eventku && php spark booking:cancel-expired >> /var/log/eventku-cron.log 2>&1
```

4. Save dan exit

## Cara Kerja

Command `booking:cancel-expired` akan:
1. Mencari semua booking dengan status `Pending` atau `Waiting Payment`
2. Yang sudah melewati `payment_deadline`
3. Auto-cancel booking tersebut
4. Mengembalikan tiket ke `available_tickets`

## Konfigurasi Deadline

Saat ini deadline diset **24 jam** dari waktu booking.

Untuk mengubah, edit file:
`app/Controllers/UserController.php` pada line:

```php
'payment_deadline' => $paymentMethod === 'manual_transfer' ? date('Y-m-d H:i:s', strtotime('+24 hours')) : null
```

Ubah `+24 hours` menjadi:
- `+12 hours` → 12 jam
- `+48 hours` → 48 jam  
- `+1 day` → 1 hari
- `+2 days` → 2 hari

## Rekomendasi

- Untuk **production**: Jalankan setiap 5-10 menit
- Untuk **development**: Jalankan manual saat testing
- Pastikan server timezone sudah benar

## Monitoring

Cek log hasil cron di:
- Windows: Task Scheduler History
- Linux: `/var/log/eventku-cron.log`
