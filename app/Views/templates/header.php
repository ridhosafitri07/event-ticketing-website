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
    
    // Dashboard CSS
    if (strpos($currentPage, 'user/dashboard') !== false): ?>
        <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
    <?php endif; ?>
    
    <?php 
    // Riwayat CSS
    if (strpos($currentPage, 'user/riwayat') !== false): ?>
        <link rel="stylesheet" href="<?= base_url('css/riwayat.css') ?>">
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
         NAVBAR / HEADER
         ======================================== -->
    <nav class="navbar">
        <div class="nav-container">
            <!-- Logo & nama app -->
            <div class="nav-brand">
                <img src="<?= base_url('images/logo tiket.png') ?>" alt="EventKu Logo" class="logo-img">
                <h2>EventKu</h2>
            </div>
            
            <!-- Hamburger menu untuk mobile -->
            <button class="hamburger-menu" onclick="toggleMobileMenu()" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <!-- Menu navigasi -->
            <div class="nav-menu" id="navMenu">
                <a href="<?= base_url('user/dashboard') ?>" class="nav-link <?= (uri_string() == 'user/dashboard') ? 'active' : '' ?>">
                    <img src="<?= base_url('images/home icon.png') ?>" alt="Home" class="nav-icon"> Home
                </a>
                <a href="<?= base_url('user/riwayat') ?>" class="nav-link <?= (uri_string() == 'user/riwayat') ? 'active' : '' ?>">
                    <img src="<?= base_url('images/riwayaticon.png') ?>" alt="Riwayat" class="nav-icon"> Riwayat
                </a>
                <a href="<?= base_url('user/profile') ?>" class="nav-link <?= (uri_string() == 'user/profile') ? 'active' : '' ?>">
                    <img src="<?= base_url('images/profil icon.jpg') ?>" alt="Profil" class="nav-icon"> Profil
                </a>
            </div>
            
            <!-- User info & logout button -->
            <div class="nav-user">
                <span><?= esc(session()->get('name') ?? 'User') ?></span>
                <a href="<?= base_url('auth/logout') ?>" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>
    <?php endif; ?>
