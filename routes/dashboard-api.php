<?php

use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\AttributeController;
use App\Http\Controllers\Dashboard\Auth\LoginController;
use App\Http\Controllers\Dashboard\Auth\ResetPasswordController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\DeliveryZoneController;
use App\Http\Controllers\Dashboard\FaqController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\ReservationSystem\BookingPolicyController;
use App\Http\Controllers\Dashboard\ReservationSystem\ClosedPeriodController;
use App\Http\Controllers\Dashboard\ReservationSystem\ReservationController;
use App\Http\Controllers\Dashboard\ReservationSystem\ReservationTypeController;
use App\Http\Controllers\Dashboard\ReservationSystem\TableController;
use App\Http\Controllers\Dashboard\ReservationSystem\WorkingShiftController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\TagController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\WalletsRechargeController;
use Illuminate\Support\Facades\Route;

/******************        Authentication APIs        ****************/
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
/******************        End Authentication APIs        ****************/


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

    Route::controller(FaqController::class)->prefix('faqs')->group(function () {
        Route::get('/', 'index');
        Route::get('/{faq}', 'show');
        Route::post('/', 'store');
        Route::post('/{faq}', 'update');
        Route::delete('/{faq}', 'destroy');
    });

    Route::controller(DeliveryZoneController::class)->prefix('zones')->group(function () {
        Route::get('/', 'index');
        Route::get('/{zone}', 'show');
        Route::post('/', 'store');
        Route::post('/{zone}', 'update');
        Route::delete('/{zone}', 'destroy');
        Route::patch('/{zone}', 'changeStatus');
    });

    Route::controller(CategoryController::class)->prefix('categories')->group(function () {
        Route::get('/', 'index');
        Route::get('/{category}', 'show');
        Route::post('/', 'store');
        Route::post('/{category}', 'update');
        Route::delete('/{category}', 'destroy');
        Route::patch('/{category}', 'changeStatus');
    });

    /********************       Reservation System      *******************/
    Route::controller(ReservationTypeController::class)->prefix('reservation-types')->group(function () {
        Route::get('/', 'index');
        Route::get('/{type}', 'show');
        Route::post('/', 'store');
        Route::post('/{type}', 'update');
        Route::delete('/{type}', 'destroy');
    });

    Route::controller(TableController::class)->prefix('tables')->group(function () {
        Route::get('/', 'index');
        Route::get('/{table}', 'show');
        Route::post('/', 'store');
        Route::post('/{table}', 'update');
        Route::delete('/{table}', 'destroy');
        Route::patch('/{table}', 'changeStatus');
    });

    Route::controller(WorkingShiftController::class)->prefix('shifts')->group(function () {
        Route::get('/', 'index');
        Route::get('/{shift}', 'show');
        Route::post('/', 'store');
        Route::post('/{shift}', 'update');
        Route::delete('/{shift}', 'destroy');
    });

    Route::controller(ClosedPeriodController::class)->prefix('periods')->group(function () {
        Route::get('/', 'index');
        Route::get('/{period}', 'show');
        Route::post('/', 'store');
        Route::post('/{period}', 'update');
        Route::delete('/{period}', 'destroy');
    });

    Route::controller(BookingPolicyController::class)->prefix('booking-policies')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'update');
    });

    Route::controller(ReservationController::class)->prefix('reservations')->group(function () {
        Route::get('/', 'index');
        Route::get('/{reservation}', 'show');
        Route::patch('/{reservation}', 'changeStatus');
        Route::delete('/{reservation}', 'destroy');
    });

    Route::controller(ProductController::class)->prefix('products')->group(function () {
        Route::get('/', 'index');
        Route::get('/{product}', 'show');
        Route::post('/{product}', 'update');
        Route::post('/', 'store');
        Route::delete('/{product}', 'destroy');
        Route::patch('/{product}', 'change');
    });

    Route::controller(OrderController::class)->prefix('orders')->group(function () {
        Route::get('/', 'index');
        Route::get('/{order}', 'show');
        Route::patch('/{order}', 'changeStatus');
        Route::delete('/{order}', 'destroy');
    });

    Route::controller(WalletsRechargeController::class)->prefix('wallets')->group(function () {
        Route::get('/recharge-requests', 'index');
        Route::patch('/recharge-requests/{request}', 'acceptOrReject');
        Route::post('/charge-manually', 'chargeManually');
        Route::delete('/{request}', 'destroy');
    });
});
