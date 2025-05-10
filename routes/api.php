<?php

use App\Http\Controllers\Dashbaoard\Auth\LoginController;
use App\Http\Controllers\Dashbaoard\Auth\OtpController;
use App\Http\Controllers\Dashbaoard\Auth\ProfileController;
use App\Http\Controllers\Dashbaoard\Auth\RegisterController;
use App\Http\Controllers\Dashbaoard\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

////////////////////////////////Auth/////////////////////////////////////////////////

Route::middleware('guest:sanctum')->group(function () {

    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [RegisterController::class, 'register']);

    Route::controller(OtpController::class)->group(function () {

        // Email Confirmation APIs
        Route::post('confirmation/email', 'sendOTP');
        Route::post('confirmation/verify', 'verify');

        // Forget Password APIs
        Route::post('passwords/email', 'sendOTP');
        Route::post('passwords/verify', 'verify');
    });
});
// Logout API (must be authenticated)
Route::post('logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');
// Reset Password API (must be authenticated)
Route::post('passwords/reset', [ResetPasswordController::class, 'reset'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/update_profile', [ProfileController::class, 'update']);
});
