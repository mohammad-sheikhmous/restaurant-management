<?php

use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\AttributeController;
use App\Http\Controllers\Dashboard\Auth\ResetPasswordController;
use App\Http\Controllers\Dashboard\Auth\LoginController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\TagController;
use App\Http\Controllers\Dashboard\UserController;
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


Route::middleware('auth:admin')->group(function () {

    Route::controller(AdminController::class)->prefix('admins')->group(function () {
        Route::get('/', 'index');
        Route::get('/{admin}', 'show');
        Route::post('/', 'store');
        Route::post('/{admin}', 'update');
        Route::delete('/{admin}', 'destroy');
        Route::patch('/{admin}', 'changeStatus');
    });

    Route::controller(RoleController::class)->prefix('roles')->group(function () {
        Route::get('/', 'index');
        Route::get('/permissions', 'getAllPermissions');
        Route::get('/{role}', 'show');
        Route::post('/', 'store');
        Route::post('/{role}', 'update');
        Route::delete('/{role}', 'destroy');
        Route::patch('/{role}', 'changeStatus');
    });

    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/', 'index');
        Route::get('/{user}', 'show');
        Route::post('/', 'store');
//        Route::post('/{user}', 'update');
        Route::delete('/{user}', 'destroy');
        Route::patch('/{user}', 'changeStatus');
    });

    Route::controller(AttributeController::class)->prefix('attributes')->group(function () {
        Route::get('/', 'index');
        Route::get('/{attribute}', 'show');
        Route::post('/', 'store');
        Route::post('/{attribute}', 'update');
        Route::delete('/{attribute}', 'destroy');
    });

    Route::controller(TagController::class)->prefix('tags')->group(function () {
        Route::get('/', 'index');
        Route::get('/{tag}', 'show');
        Route::post('/', 'store');
        Route::post('/{tag}', 'update');
        Route::delete('/{tag}', 'destroy');
    });
});
