<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Auth Routes
$routes->get('/', 'AuthController::index');
$routes->get('/auth/login', 'AuthController::index');
$routes->get('/auth/register', 'AuthController::register');
$routes->post('/auth/doLogin', 'AuthController::doLogin');
$routes->post('/auth/doRegister', 'AuthController::doRegister');
$routes->get('/auth/logout', 'AuthController::logout');

// User Routes (perlu login)
$routes->group('user', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'UserController::dashboard');
    $routes->get('booking/(:num)', 'UserController::booking/$1');
    $routes->post('booking/process', 'UserController::processBooking');
    $routes->get('riwayat', 'UserController::riwayat');
    $routes->get('profile', 'UserController::profile');
    $routes->post('profile/update', 'UserController::updateProfile');
});

// Payment Routes
$routes->post('payment/upload/(:num)', 'PaymentController::uploadProof/$1');

// Admin Routes
$routes->get('admin', 'AdminController::login');
$routes->post('admin/doLogin', 'AdminController::doLogin');
$routes->group('admin', ['filter' => 'adminAuth'], function($routes) {
    $routes->get('dashboard', 'AdminController::dashboard');
    $routes->get('bookings', 'AdminController::bookings');
    $routes->post('approve/(:num)', 'AdminController::approvePayment/$1');
});
