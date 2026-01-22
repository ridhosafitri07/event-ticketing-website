<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'EventKu - Platform Ticketing Terbaik') ?></title>
    
    <!-- Base CSS - ALWAYS LOAD FIRST -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    
    <!-- Page-specific CSS - Load based on current page -->
    <?php 
    $currentPage = uri_string();
    
    // User Dashboard CSS (KHUSUS USER - untuk dashboard dan statistics)
    if (strpos($currentPage, 'user/dashboard') !== false || strpos($currentPage, 'user/statistics') !== false): ?>
        <link rel="stylesheet" href="<?= base_url('css/user-dashboard.css') ?>">
    <?php endif; ?>
    
    <?php 
    // Riwayat CSS (KHUSUS USER)
    if (strpos($currentPage, 'user/riwayat') !== false): ?>
        <link rel="stylesheet" href="<?= base_url('css/riwayat.css') ?>">
    <?php endif; ?>
    
    <?php 
    // Payment CSS (untuk halaman payment/transfer)
    if (strpos($currentPage, 'payment') !== false): ?>
        <link rel="stylesheet" href="<?= base_url('css/payment-premium.css') ?>">
    <?php endif; ?>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('images/logo tiket.png') ?>">
    
    <!-- Meta tags -->
    <meta name="description" content="EventKu - Platform pembelian tiket event terpercaya">
    <meta name="author" content="EventKu">
</head>
<body class="<?= esc($bodyClass ?? '') ?>">
    
    
    <?php if (isset($showNavbar) && $showNavbar): ?>
    <!-- ========================================
         MODERN PREMIUM NAVBAR - EventKu
         ======================================== -->
    <nav class="navbar-premium">
        <div class="navbar-glow"></div>
        <div class="nav-container-premium">
            <!-- Logo & Brand -->
            <div class="nav-brand-premium">
                <div class="logo-wrapper">
                    <img src="<?= base_url('images/logo tiket.png') ?>" alt="EventKu Logo" class="logo-img-premium">
                    <div class="logo-pulse"></div>
                </div>
                <h2 class="brand-text">Event<span class="brand-accent">Ku</span></h2>
            </div>
            
            <!-- Hamburger menu untuk mobile -->
            <button class="hamburger-premium" onclick="toggleMobileMenu()" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <!-- Menu navigasi tengah -->
            <div class="nav-menu-premium" id="navMenu">
                <a href="<?= base_url('user/dashboard') ?>" class="nav-link-premium <?= (uri_string() == 'user/dashboard') ? 'active' : '' ?>">
                    <svg class="nav-icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Home</span>
                </a>
                
                <a href="<?= base_url('user/favorites') ?>" class="nav-link-premium <?= (uri_string() == 'user/favorites') ? 'active' : '' ?>">
                    <svg class="nav-icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M20.84 4.61C20.3292 4.099 19.7228 3.69364 19.0554 3.41708C18.3879 3.14052 17.6725 2.99817 16.95 2.99817C16.2275 2.99817 15.5121 3.14052 14.8446 3.41708C14.1772 3.69364 13.5708 4.099 13.06 4.61L12 5.67L10.94 4.61C9.9083 3.57831 8.50903 2.99871 7.05 2.99871C5.59096 2.99871 4.19169 3.57831 3.16 4.61C2.1283 5.64169 1.54871 7.04097 1.54871 8.5C1.54871 9.95903 2.1283 11.3583 3.16 12.39L4.22 13.45L12 21.23L19.78 13.45L20.84 12.39C21.351 11.8792 21.7564 11.2728 22.0329 10.6053C22.3095 9.93789 22.4518 9.22248 22.4518 8.5C22.4518 7.77752 22.3095 7.0621 22.0329 6.39464C21.7564 5.72718 21.351 5.12075 20.84 4.61V4.61Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Favorit</span>
                </a>
                
                <a href="<?= base_url('user/riwayat') ?>" class="nav-link-premium <?= (uri_string() == 'user/riwayat') ? 'active' : '' ?>">
                    <svg class="nav-icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Riwayat</span>
                </a>
                
                <a href="<?= base_url('user/statistics') ?>" class="nav-link-premium <?= (uri_string() == 'user/statistics') ? 'active' : '' ?>">
                    <svg class="nav-icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M18 20V10M12 20V4M6 20V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Statistik</span>
                </a>
                
                <a href="<?= base_url('user/profile') ?>" class="nav-link-premium <?= (uri_string() == 'user/profile') ? 'active' : '' ?>">
                    <svg class="nav-icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Profil</span>
                </a>
            </div>
            
            <!-- User info & logout button -->
            <div class="nav-user-premium">
                <div class="user-info-premium">
                    <div class="user-avatar">
                        <?= strtoupper(substr(session()->get('name') ?? 'U', 0, 1)) ?>
                    </div>
                    <span class="user-name"><?= esc(session()->get('name') ?? 'User') ?></span>
                </div>
                <a href="<?= base_url('auth/logout') ?>" class="btn-logout-premium">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </nav>
    <?php endif; ?>
