<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TelegramSetupController;
use Illuminate\Support\Facades\Route;

// Homepage
Route::get('/', function (\Illuminate\Http\Request $request) {
    if ($request->has('ref')) {
        return app(AffiliateController::class)->captureReferral($request);
    }
    return app(HomeController::class)->index($request);
})->name('home');

// Shop / Product Catalog
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');

// Product detail & share page
Route::get('/products/{slug}', [HomeController::class, 'showProduct'])->name('product.show');

//Auth (Register & Login) — hanya untuk guest
Route::middleware('guest')->group(function () {
    Route::get('/register',  [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/login',     [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Telegram setup page — untuk user yang baru login/register tanpa chat_id
Route::middleware('auth')->group(function () {
    Route::get('/telegram/setup', [TelegramSetupController::class, 'show'])->name('telegram.setup');
});

// Profil — untuk user yang sudah login
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/',         [ProfileController::class, 'edit'])->name('edit');
    Route::put('/',         [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
});

//Order Tracking — harus login
Route::middleware('auth')->group(function () {
    Route::get('/orders',               [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderNumber}', [OrderController::class, 'track'])->name('orders.track');
});

// Checkout — harus login sebagai customer/affiliate
Route::prefix('checkout')->name('checkout.')->middleware('auth')->group(function () {
    Route::get('/',        [CheckoutController::class, 'showForm'])->name('form');
    Route::post('/',       [CheckoutController::class, 'process'])->name('process');
    Route::get('/success', [CheckoutController::class, 'success'])->name('success');
    Route::get('/failed',  [CheckoutController::class, 'failed'])->name('failed');
});

// Cart — harus login
Route::prefix('cart')->name('cart.')->middleware('auth')->group(function () {
    Route::get('/',              [CartController::class, 'index'])->name('index');
    Route::post('/add',          [CartController::class, 'add'])->name('add');
    Route::patch('/{productId}', [CartController::class, 'update'])->name('update');
    Route::delete('/clear',      [CartController::class, 'clear'])->name('clear');
    Route::delete('/{productId}',[CartController::class, 'remove'])->name('remove');
});

// Affiliate
Route::prefix('affiliate')->name('affiliate.')->group(function () {
    Route::get('/register',  [AffiliateController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/register', [AffiliateController::class, 'register'])->name('register');
    Route::get('/dashboard', [AffiliateController::class, 'dashboard'])->name('dashboard');
    Route::put('/payout',    [AffiliateController::class, 'updatePayout'])->name('payout');
    Route::post('/withdraw', [AffiliateController::class, 'requestWithdrawal'])->name('withdraw');
});

// Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',  [AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

        Route::get('/dashboard',             [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders',                [AdminController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}',        [AdminController::class, 'showOrder'])->name('orders.show');
        Route::put('/orders/{order}/status',   [AdminController::class, 'updateStatus'])->name('orders.status');
        Route::post('/orders/{order}/notify',   [AdminController::class, 'sendNotification'])->name('orders.notify');
        Route::post('/orders/{order}/simulate-payment', [AdminController::class, 'simulatePayment'])->name('orders.simulate-payment');
        Route::get('/affiliates',            [AdminController::class, 'affiliates'])->name('affiliates');
        Route::post('/affiliates/{affiliate}/approve', [AdminController::class, 'approveAffiliate'])->name('affiliates.approve');
        Route::post('/affiliates/{affiliate}/reject',  [AdminController::class, 'rejectAffiliate'])->name('affiliates.reject');
        Route::get('/withdrawals',                          [AdminController::class, 'withdrawals'])->name('withdrawals');
        Route::post('/withdrawals/{withdrawal}/approve',    [AdminController::class, 'approveWithdrawal'])->name('withdrawals.approve');
        Route::post('/withdrawals/{withdrawal}/reject',     [AdminController::class, 'rejectWithdrawal'])->name('withdrawals.reject');
        Route::get('/notifications',         [AdminController::class, 'notifications'])->name('notifications');
        // Products
        Route::get('/products',              [AdminController::class, 'products'])->name('products');
        Route::get('/products/create',       [AdminController::class, 'createProduct'])->name('products.create');
        Route::post('/products',             [AdminController::class, 'storeProduct'])->name('products.store');
        Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
        Route::put('/products/{product}',    [AdminController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('products.delete');
        // Users
        Route::get('/users',                 [AdminController::class, 'users'])->name('users');
        // Audit Log
        Route::get('/audit-log',             [AdminController::class, 'auditLog'])->name('audit-log');
        // Commissions
        Route::get('/commissions',           [AdminController::class, 'commissions'])->name('commissions');
    });
});

// ---------------------------------------------------------------------------
// Webhook — lihat routes/api.php (POST /api/webhook/payment)
// ---------------------------------------------------------------------------

