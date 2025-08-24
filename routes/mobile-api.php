<?php

use App\Http\Controllers\Dashboard\FaqController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Mobile\Auth\LoginController;
use App\Http\Controllers\Mobile\Auth\OtpController;
use App\Http\Controllers\Mobile\Auth\ProfileController;
use App\Http\Controllers\Mobile\Auth\RegisterController;
use App\Http\Controllers\Mobile\Auth\ResetPasswordController;
use App\Http\Controllers\Mobile\CartController;
use App\Http\Controllers\Mobile\CategoryController;
use App\Http\Controllers\Mobile\DeliveryZoneController;
use App\Http\Controllers\Mobile\OrderController;
use App\Http\Controllers\Mobile\ProductController;
use App\Http\Controllers\Mobile\ReservationController;
use App\Http\Controllers\Mobile\TagController;
use App\Http\Controllers\Mobile\UserAddressController;
use App\Http\Controllers\Mobile\WalletController;
use App\Http\Controllers\Mobile\WishlistController;
use Illuminate\Support\Facades\Route;

/*****************        Authentication APIs        ********************/

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

/*****************        End Authentication APIs        ********************/

Route::get('images/{path}', ImageController::class)->where('path', '.*');
Route::get('/faqs', [FaqController::class, 'index']);
Route::get('tags', [TagController::class, 'index']);
Route::get('zones', [DeliveryZoneController::class, 'index']);

Route::controller(CategoryController::class)->prefix('categories')->group(function () {
    Route::get('', 'index');
    Route::get('{id}/products', 'getCategoryProducts');
});

Route::controller(ProductController::class)->prefix('products')->group(function () {
    Route::get('search', 'getProductsBySearching');
    Route::get('home', 'getProductsForHome');
    Route::get('{id}', 'show');
});

Route::controller(WishlistController::class)->prefix('wishlists')->group(function () {
    Route::get('/', 'show');
    Route::post('add-product', 'addProduct');
    Route::post('remove-product', 'removeProduct');
});

Route::controller(CartController::class)->prefix('carts')->group(function () {
    Route::get('/', 'show');
    Route::post('items', 'addItem');
    Route::get('items/{id}', 'showItem');
    Route::post('items/{id}', 'updateItem');
    Route::delete('items/{id}', 'removeItem');
    Route::patch('items/{id}/increment', 'incrementItem');
    Route::patch('items/{id}/decrement', 'decrementItem');
});

Route::middleware('auth:user')->group(function () {

    Route::controller(ProfileController::class)->prefix('profile')->group(function () {
        Route::get('/', 'show');
        Route::post('/', 'update');
        Route::post('/update-image', 'updateUserImage');
    });

    Route::controller(OrderController::class)->prefix('orders')->group(function () {
        Route::get('/', 'index');
        Route::get('creating-details', 'getDetailsForCreatingOrder');
        Route::get('/{id}', 'show');
        Route::post('/', 'store');
        Route::patch('/{id}', 'cancel');
        Route::delete('/{id}', 'destroy');
        Route::get('/reorder/{id}', 'reorder');
    });

    Route::controller(UserAddressController::class)->prefix('addresses')->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::post('/', 'store');
        Route::delete('/{id}', 'destroy');
    });

    Route::controller(ReservationController::class)->prefix('reservations')->group(function () {
        Route::get('/available-days', 'getAvailableDays');
        Route::get('/available-time-slots', 'getAvailableTimeSlots');
        Route::post('/temp-revs', 'createTempRevs');
        Route::post('/confirm-temp-revs/{id}', 'confirmTempRevs');
        Route::patch('/{id}/cancel', 'cancel');
        Route::get('/', 'index');
        Route::get('/{id}/edit', 'edit');
        Route::post('/{id}', 'update');
        Route::get('/{id}', 'show');
    });

    Route::controller(WalletController::class)->prefix('wallet')->group(function () {
        Route::get('/', 'getWallet');
        Route::post('/charge', 'charge');
    });
});
