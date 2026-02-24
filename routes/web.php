<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------------------
// Homepage — capture referral jika ada ?ref=CODE
// ---------------------------------------------------------------------------
Route::get('/', function (\Illuminate\Http\Request $request) {
    if ($request->has('ref')) {
        return app(AffiliateController::class)->captureReferral($request);
    }
    return app(HomeController::class)->index($request);
})->name('home');

// ---------------------------------------------------------------------------
// Shop / Product Catalog
// ---------------------------------------------------------------------------
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');

// ---------------------------------------------------------------------------
// Auth (Register & Login) — hanya untuk guest
// ---------------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/register',  [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/login',     [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---------------------------------------------------------------------------
// Profil — untuk user yang sudah login
// ---------------------------------------------------------------------------
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/',           [ProfileController::class, 'edit'])->name('edit');
    Route::put('/',           [ProfileController::class, 'update'])->name('update');
    Route::put('/password',   [ProfileController::class, 'updatePassword'])->name('password');
    Route::post('/telegram',  [ProfileController::class, 'saveTelegramId'])->name('telegram');
});

// ---------------------------------------------------------------------------
// Checkout — harus login sebagai customer/affiliate
// ---------------------------------------------------------------------------
Route::prefix('checkout')->name('checkout.')->middleware('auth')->group(function () {
    Route::get('/',        [CheckoutController::class, 'showForm'])->name('form');
    Route::post('/',       [CheckoutController::class, 'process'])->name('process');
    Route::get('/success', [CheckoutController::class, 'success'])->name('success');
    Route::get('/failed',  [CheckoutController::class, 'failed'])->name('failed');
});

// ---------------------------------------------------------------------------
// Riwayat Pesanan (Customer)
// ---------------------------------------------------------------------------
Route::middleware('auth')->name('orders.')->group(function () {
    Route::get('/my-orders',        [HomeController::class, 'myOrders'])->name('index');
    Route::get('/my-orders/{order}',[HomeController::class, 'myOrderDetail'])->name('show');
});

// ---------------------------------------------------------------------------
// Affiliate
// ---------------------------------------------------------------------------
Route::prefix('affiliate')->name('affiliate.')->group(function () {
    Route::get('/register',  [AffiliateController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/register', [AffiliateController::class, 'register'])->name('register');
    Route::get('/dashboard', [AffiliateController::class, 'dashboard'])->name('dashboard');
    Route::put('/payout',    [AffiliateController::class, 'updatePayout'])->name('payout');
    Route::post('/request-payout', [AffiliateController::class, 'requestPayout'])->name('payout.request');
});

// ---------------------------------------------------------------------------
// Admin
// ---------------------------------------------------------------------------
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',  [AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

        Route::get('/dashboard',             [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders',                [AdminController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}',        [AdminController::class, 'showOrder'])->name('orders.show');
        Route::put('/orders/{order}/status',   [AdminController::class, 'updateStatus'])->name('orders.status');
        Route::put('/orders/{order}/tracking', [AdminController::class, 'updateTracking'])->name('orders.tracking');
        Route::post('/orders/{order}/notify',   [AdminController::class, 'sendNotification'])->name('orders.notify');
        Route::post('/orders/{order}/simulate-payment', [AdminController::class, 'simulatePayment'])->name('orders.simulate-payment');
        Route::get('/affiliates',            [AdminController::class, 'affiliates'])->name('affiliates');
        Route::post('/affiliates/{affiliate}/approve', [AdminController::class, 'approveAffiliate'])->name('affiliates.approve');
        Route::post('/affiliates/{affiliate}/reject',  [AdminController::class, 'rejectAffiliate'])->name('affiliates.reject');
        Route::get('/notifications',         [AdminController::class, 'notifications'])->name('notifications');
    });
});

// ---------------------------------------------------------------------------
// Webhook — lihat routes/api.php (POST /api/webhook/payment)
// ---------------------------------------------------------------------------

