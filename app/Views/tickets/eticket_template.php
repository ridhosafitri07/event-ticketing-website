<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>E-Ticket <?= $booking['booking_number'] ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: #f8fafc;
            padding: 20px;
        }
        
        .ticket-container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .ticket-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .ticket-header h1 {
            font-size: 28px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .ticket-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .event-banner {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        
        .ticket-body {
            padding: 30px;
        }
        
        .event-title {
            font-size: 24px;
            color: #1e293b;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        
        .info-row {
            display: table-row;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .info-label {
            display: table-cell;
            padding: 12px 0;
            color: #64748b;
            font-size: 14px;
            width: 40%;
        }
        
        .info-value {
            display: table-cell;
            padding: 12px 0;
            color: #1e293b;
            font-size: 14px;
            font-weight: 600;
        }
        
        .booking-info {
            background: #f1f5f9;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        
        .booking-number {
            font-size: 20px;
            color: #667eea;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .qr-section {
            text-align: center;
            margin: 25px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 12px;
        }
        
        .qr-code {
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }
        
        .qr-label {
            color: #64748b;
            font-size: 12px;
            margin-top: 10px;
        }
        
        .ticket-footer {
            background: #f8fafc;
            padding: 20px 30px;
            border-top: 2px dashed #cbd5e1;
            text-align: center;
        }
        
        .footer-note {
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
        }
        
        .important-note {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        
        .important-note p {
            font-size: 13px;
            color: #92400e;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <!-- Header -->
        <div class="ticket-header">
            <h1>🎟️ E-TICKET</h1>
            <p>Web Ticketing Event</p>
        </div>
        
        <!-- Event Banner -->
        <?php if (!empty($event['image'])): ?>
            <img src="<?= base_url('uploads/events/' . $event['image']) ?>" alt="Event" class="event-banner">
        <?php endif; ?>
        
        <!-- Body -->
        <div class="ticket-body">
            <div class="event-title"><?= esc($event['title']) ?></div>
            
            <!-- Event Info -->
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">📅 Tanggal Event</div>
                    <div class="info-value"><?= date('d F Y', strtotime($event['date'])) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">� Lokasi</div>
                    <div class="info-value"><?= esc($event['location']) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">🎫 Kategori</div>
                    <div class="info-value"><?= esc($event['category']) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">👤 Nama Pemesan</div>
                    <div class="info-value"><?= esc($user['name']) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">📧 Email</div>
                    <div class="info-value"><?= esc($user['email']) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">🎫 Jumlah Tiket</div>
                    <div class="info-value"><?= $booking['ticket_count'] ?> Tiket</div>
                </div>
                <div class="info-row">
                    <div class="info-label">💰 Total Pembayaran</div>
                    <div class="info-value">Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></div>
                </div>
            </div>
            
            <!-- Booking Number -->
            <div class="booking-info">
                <div style="color: #64748b; font-size: 12px; margin-bottom: 5px;">Nomor Booking</div>
                <div class="booking-number"><?= $booking['booking_number'] ?></div>
                <div style="color: #64748b; font-size: 11px; margin-top: 5px;">
                    Dipesan pada: <?= date('d M Y H:i', strtotime($booking['booking_date'])) ?> WIB
                </div>
            </div>
            
            <!-- QR Code -->
            <div class="qr-section">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode($qrData) ?>" 
                     alt="QR Code" class="qr-code">
                <div class="qr-label">Scan QR Code ini saat check-in</div>
            </div>
            
            <!-- Important Note -->
            <div class="important-note">
                <p><strong>⚠️ PENTING:</strong></p>
                <p>
                    • Tunjukkan e-ticket ini saat masuk event<br>
                    • Simpan e-ticket dengan baik, tidak dapat diprint ulang<br>
                    • E-ticket ini hanya berlaku untuk 1x penggunaan<br>
                    • Datang <?= $booking['ticket_count'] > 1 ? $booking['ticket_count'] . ' orang sesuai jumlah tiket' : '1 orang' ?>
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="ticket-footer">
            <div class="footer-note">
                <strong>Terima kasih telah memesan!</strong><br>
                Jika ada pertanyaan, hubungi panitia event.<br>
                <small style="color: #94a3b8;">E-Ticket ini dibuat otomatis oleh sistem</small>
            </div>
        </div>
    </div>
</body>
</html>
