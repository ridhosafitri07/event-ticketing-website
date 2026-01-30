<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket - <?= esc($booking['event_title']) ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ticket-container {
            max-width: 800px;
            width: 100%;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .ticket-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .ticket-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .event-badge {
            display: inline-block;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            color: white;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
        }

        .event-title {
            font-size: 32px;
            font-weight: 800;
            color: white;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .event-subtitle {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ticket-divider {
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, #e2e8f0 10%, #e2e8f0 90%, transparent 100%);
            position: relative;
            margin: 0 -40px;
        }

        .ticket-divider::before,
        .ticket-divider::after {
            content: '';
            position: absolute;
            top: -10px;
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
        }

        .ticket-divider::before {
            left: -10px;
        }

        .ticket-divider::after {
            right: -10px;
        }

        .ticket-body {
            padding: 40px;
        }

        .ticket-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
            margin-bottom: 32px;
        }

        .info-section {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .info-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #f0f4ff 0%, #e8f0ff 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .info-icon svg {
            width: 24px;
            height: 24px;
            color: #667eea;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            line-height: 1.4;
        }

        .qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            padding: 24px;
            border: 2px dashed #cbd5e1;
        }

        .qr-code {
            width: 180px;
            height: 180px;
            background: white;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .qr-code img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .booking-number {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
        }

        .ticket-details {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .detail-item {
            text-align: center;
        }

        .detail-label {
            font-size: 11px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .detail-value {
            font-size: 20px;
            font-weight: 800;
            color: #1e293b;
        }

        .detail-value.price {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .ticket-footer {
            border-top: 2px dashed #e2e8f0;
            padding-top: 24px;
            text-align: center;
        }

        .footer-logo {
            font-size: 20px;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .footer-text {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-radius: 24px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .status-icon {
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .ticket-container {
                box-shadow: none;
                max-width: 100%;
            }

            .ticket-header::before {
                display: none;
            }
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            body {
                padding: 20px 10px;
            }

            .ticket-header {
                padding: 24px 20px;
            }

            .event-title {
                font-size: 24px;
            }

            .ticket-body {
                padding: 24px 20px;
            }

            .ticket-grid {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .qr-section {
                order: -1;
            }

            .details-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .detail-item {
                text-align: left;
                padding: 16px;
                background: white;
                border-radius: 12px;
            }

            .info-row {
                padding: 16px;
                background: white;
                border-radius: 12px;
            }

            .qr-code {
                width: 160px;
                height: 160px;
            }
        }

        /* Animation */
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

        .ticket-container {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <!-- Header -->
        <div class="ticket-header">
            <div class="header-content">
                <div class="event-badge">🎫 E-Ticket EventKu</div>
                <h1 class="event-title"><?= esc($booking['event_title']) ?></h1>
                <div class="event-subtitle">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <?= esc($booking['event_location']) ?>
                </div>
            </div>
        </div>

        <div class="ticket-divider"></div>

        <!-- Body -->
        <div class="ticket-body">
            <!-- Status Badge -->
            <div class="status-badge">
                <div class="status-icon">✓</div>
                <span>Ticket Confirmed</span>
            </div>

            <!-- Main Info Grid -->
            <div class="ticket-grid">
                <!-- Left: Event Details -->
                <div class="info-section">
                    <div class="info-row">
                        <div class="info-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Event Date</div>
                            <div class="info-value"><?= esc($booking['event_date']) ?></div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Ticket Holder</div>
                            <div class="info-value"><?= esc($user['name']) ?></div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?= esc($user['email']) ?></div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Phone</div>
                            <div class="info-value"><?= esc($user['phone']) ?></div>
                        </div>
                    </div>
                </div>

                <!-- Right: QR Code -->
                <div class="qr-section">
                    <div class="qr-code">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=<?= urlencode($booking['booking_number']) ?>" 
                             alt="QR Code">
                    </div>
                    <div class="booking-number"><?= esc($booking['booking_number']) ?></div>
                </div>
            </div>

            <!-- Ticket Details -->
            <div class="ticket-details">
                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-label">Tickets</div>
                        <div class="detail-value"><?= $booking['ticket_count'] ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Total Amount</div>
                        <div class="detail-value price">Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Booked On</div>
                        <div class="detail-value"><?= date('d M Y', strtotime($booking['booking_date'])) ?></div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="ticket-footer">
                <div class="footer-logo">🎫 EventKu</div>
                <p class="footer-text">
                    This is your official e-ticket. Please present this QR code at the event entrance.<br>
                    For assistance, contact us at support@eventku.com
                </p>
            </div>
        </div>
    </div>

    <script>
        // Auto print when loaded (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>