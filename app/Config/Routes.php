<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================
// LANDING PAGE & AUTH ROUTES
// ============================================

// Landing page → Login
$routes->get('/', 'AuthController::index');

// Auth Routes
$routes->get('auth/login', 'AuthController::index');
$routes->get('auth/register', 'AuthController::register');
$routes->get('auth/forgot-password', 'AuthController::forgotPassword');
$routes->post('auth/doLogin', 'AuthController::doLogin');
$routes->post('auth/doRegister', 'AuthController::doRegister');
$routes->post('auth/doForgotPassword', 'AuthController::doForgotPassword');
$routes->get('auth/logout', 'AuthController::logout');

// ============================================
// USER ROUTES (Protected by AuthFilter)
// ============================================

$routes->group('user', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'UserController::dashboard');
    
    // Booking
    $routes->get('booking/(:num)', 'UserController::booking/$1');
    $routes->post('booking/process', 'UserController::processBooking');
    
    // Riwayat & Cancel
    $routes->get('riwayat', 'UserController::riwayat');
    $routes->get('cancelBooking/(:num)', 'UserController::cancelBooking/$1');
    
    // Profile
    $routes->get('profile', 'UserController::profile');
    $routes->post('profile/update', 'UserController::updateProfile');
});

// ============================================
// PAYMENT ROUTES
// ============================================

// Halaman instruksi transfer manual
$routes->get('payment/manual/(:num)', 'PaymentController::manualTransfer/$1');

// Upload Bukti Transfer (Manual)
$routes->post('payment/upload/(:num)', 'PaymentController::uploadProof/$1');

// Midtrans Payment Gateway
$routes->get('payment/midtrans/(:num)', 'PaymentController::midtransCheckout/$1');
$routes->post('payment/midtrans/callback', 'PaymentController::midtransCallback');

// Payment Status Pages
$routes->get('payment/success', 'PaymentController::paymentSuccess');
$routes->get('payment/failed', 'PaymentController::paymentFailed');

// ============================================
// ADMIN ROUTES (Protected by AdminAuthFilter)
// ============================================

// Admin Login (Public)
$routes->get('admin', 'AdminController::login');
$routes->get('admin/login', 'AdminController::login');
$routes->post('admin/doLogin', 'AdminController::doLogin');

// Admin Protected Routes
$routes->group('admin', ['filter' => 'adminAuth'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'AdminController::dashboard');
    
    // Manage Bookings
    $routes->get('bookings', 'AdminController::bookings');
    $routes->post('approve/(:num)', 'AdminController::approvePayment/$1');
    $routes->post('reject/(:num)', 'AdminController::rejectPayment/$1');
    $routes->get('payment-proof/(:num)', 'AdminController::viewPaymentProof/$1');
    
    // Manage Events (CRUD)
    $routes->get('events', 'AdminController::events');
    $routes->get('event/create', 'AdminController::createEvent');
    $routes->get('event/edit/(:num)', 'AdminController::editEvent/$1');
    $routes->post('event/save', 'AdminController::saveEvent');
    $routes->get('event/delete/(:num)', 'AdminController::deleteEvent/$1');
    
    // Manage Users
    $routes->get('users', 'AdminController::users');
    $routes->get('users/(:num)', 'AdminController::userDetail/$1');
    
    // Manage Payments
    $routes->get('payments', 'AdminController::payments');
    
    // Settings
    $routes->get('settings', 'AdminController::settings');
    $routes->post('settings/profile', 'AdminController::updateProfile');
    $routes->post('settings/password', 'AdminController::changePassword');
    
    // Logout
    $routes->get('logout', 'AdminController::logout');
});