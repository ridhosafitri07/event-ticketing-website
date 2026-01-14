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
    // Set via environment variable (recommended): MIDTRANS_SERVER_KEY
    public string $serverKey;
    
    /**
     * Midtrans Client Key
     */
    // Set via environment variable (recommended): MIDTRANS_CLIENT_KEY
    public string $clientKey;
    
    /**
     * Environment: 'sandbox' atau 'production'
     */
    // MIDTRANS_ENV=sandbox|production
    public string $environment;
    
    /**
     * Merchant ID
     */
    // MIDTRANS_MERCHANT_ID
    public string $merchantId;
    
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

    public function __construct()
    {
        $this->serverKey = (string) (getenv('MIDTRANS_SERVER_KEY') ?: '');
        $this->clientKey = (string) (getenv('MIDTRANS_CLIENT_KEY') ?: '');
        $this->merchantId = (string) (getenv('MIDTRANS_MERCHANT_ID') ?: '');
        $this->environment = (string) (getenv('MIDTRANS_ENV') ?: 'sandbox');

        $is3ds = getenv('MIDTRANS_IS3DS');
        if ($is3ds !== false && $is3ds !== '') {
            $this->is3ds = filter_var($is3ds, FILTER_VALIDATE_BOOL);
        }
    }
    
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