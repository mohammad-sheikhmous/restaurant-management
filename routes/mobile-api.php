<?php

use App\Http\Controllers\Mobile\Auth\LoginController;
use App\Http\Controllers\Mobile\Auth\OtpController;
use App\Http\Controllers\Mobile\Auth\ProfileController;
use App\Http\Controllers\Mobile\Auth\RegisterController;
use App\Http\Controllers\Mobile\Auth\ResetPasswordController;
use App\Http\Controllers\Mobile\CategoryController;
use App\Http\Controllers\Mobile\ProductController;
use App\Http\Controllers\Mobile\TagController;
use Illuminate\Support\Facades\Route;

///////////////////         Authentication APIs        ///////////////////////

Route::controller(LoginController::class)->group(function () {

    Route::post('login', 'login');
    // Logout API (must be authenticated)
    Route::post('logout', 'logout');
});
Route::post('register', [RegisterController::class, 'register']);

Route::controller(OtpController::class)->group(function () {

    // Email Confirmation APIs
    Route::post('confirmation/email', 'sendOTP');
    Route::post('confirmation/verify', 'verify');

    // Forget Password APIs
    Route::post('passwords/email', 'sendOTP');
    Route::post('passwords/verify', 'verify');
});
// Reset Password API (must be authenticated)
Route::post('passwords/reset', [ResetPasswordController::class, 'reset']);

///////////////////        End Authentication APIs        ///////////////////////

Route::get('tags', [TagController::class, 'index']);
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{id}/products', [CategoryController::class, 'getCategoryProducts']);
Route::get('products/search', [ProductController::class, 'getProductsBySearching']);
Route::get('products/home', [ProductController::class, 'getProductsForHome']);

Route::middleware('auth:user')->group(function () {

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/update_profile', [ProfileController::class, 'update']);
});
