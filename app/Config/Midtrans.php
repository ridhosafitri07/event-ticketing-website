<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Midtrans extends BaseConfig
{
    /**
     * Midtrans Server Key (dari Midtrans Dashboard)
     * Sandbox: https://dashboard.sandbox.midtrans.com/
     * Production: https://dashboard.midtrans.com/
     */
    public string $serverKey = 'SB-Mid-server-YOUR_SERVER_KEY_HERE';
    
    /**
     * Midtrans Client Key
     */
    public string $clientKey = 'SB-Mid-client-YOUR_CLIENT_KEY_HERE';
    
    /**
     * Environment: 'sandbox' atau 'production'
     */
    public string $environment = 'sandbox';
    
    /**
     * Merchant ID
     */
    public string $merchantId = 'YOUR_MERCHANT_ID';
    
    /**
     * Enable 3D Secure for credit card
     */
    public bool $is3ds = true;
    
    /**
     * Append notification (optional)
     */
    public bool $appendNotification = false;
    
    /**
     * Override notification URL (optional)
     */
    public ?string $overrideNotificationUrl = null;
    
    /**
     * Payment methods yang diaktifkan
     * QRIS sebagai default utama
     */
    public array $enabledPayments = [
        'qris',           // QR Code (GoPay, OVO, Dana, ShopeePay, dll)
        'gopay',          // GoPay wallet
        'shopeepay',      // ShopeePay
        'other_qris',     // QRIS lainnya
    ];
    
    /**
     * Get Snap API URL
     */
    public function getSnapUrl(): string
    {
        return $this->environment === 'production' 
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }
    
    /**
     * Get API URL
     */
    public function getApiUrl(): string
    {
        return $this->environment === 'production'
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }
}