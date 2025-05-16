<?php

use App\Http\Controllers\Dashboard\Auth\ResetPasswordController;
use App\Http\Controllers\Dashboard\Auth\LoginController;
use Illuminate\Support\Facades\Route;

///////////////////         Authentication APIs        ///////////////////////

Route::controller(LoginController::class)->group(function () {
    // Login API (must be guest)
    Route::post('login', 'login');

    // Logout API (must be authenticated)
    Route::post('logout', 'logout');
});

Route::controller(ResetPasswordController::class)->group(function () {
    // Forget Password APIs
    Route::post('passwords/email', 'sendOTP');
    Route::post('passwords/verify-otp', 'verifyOTP');

    // Reset Password API (must be authenticated)
    Route::post('passwords/reset', 'reset');
});
///////////////////        End Authentication APIs        ///////////////////////

