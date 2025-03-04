<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

 $routes->get('/', 'OtpController::index');
 $routes->post('send-otp', 'OtpController::sendOtp');  // No leading `/`
 $routes->post('verify-otp', 'OtpController::verifyOtp');
 $routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
 $routes->post('/logout', 'DashboardController::logout');

 



