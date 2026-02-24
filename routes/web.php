<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------------------
// Homepage — capture referral jika ada ?ref=CODE
// ---------------------------------------------------------------------------
Route::get('/', function (\Illuminate\Http\Request $request) {
    if ($request->has('ref')) {
        return app(AffiliateController::class)->captureReferral($request);
    }
    return app(HomeController::class)->index();
})->name('home');

// ---------------------------------------------------------------------------
// Checkout
// ---------------------------------------------------------------------------
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/',        [CheckoutController::class, 'showForm'])->name('form');
    Route::post('/',       [CheckoutController::class, 'process'])->name('process');
    Route::get('/success', [CheckoutController::class, 'success'])->name('success');
    Route::get('/failed',  [CheckoutController::class, 'failed'])->name('failed');
});

// ---------------------------------------------------------------------------
// Affiliate
// ---------------------------------------------------------------------------
Route::prefix('affiliate')->name('affiliate.')->group(function () {
    Route::get('/register',  [AffiliateController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/register', [AffiliateController::class, 'register'])->name('register');
    Route::get('/dashboard', [AffiliateController::class, 'dashboard'])->name('dashboard');
});

// ---------------------------------------------------------------------------
// Admin
// ---------------------------------------------------------------------------
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',  [AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');

    // Protected admin routes
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

        Route::get('/dashboard',             [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders',                [AdminController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}',        [AdminController::class, 'showOrder'])->name('orders.show');
        Route::put('/orders/{order}/status', [AdminController::class, 'updateStatus'])->name('orders.status');
        Route::get('/affiliates',            [AdminController::class, 'affiliates'])->name('affiliates');
        Route::get('/notifications',         [AdminController::class, 'notifications'])->name('notifications');
    });
});
