<?php

use App\Http\Controllers\AffiliateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// TODO [PHASE 2 - Ghufron]: Tangkap referral jika URL mengandung ?ref=CODE
Route::get('/', function () {
    return view('welcome');
    // TODO [PHASE 2 - Ghufron]: Ganti atau tambahkan referral capture di sini
});

// --- Affiliate Routes ---
// TODO [PHASE 2 - Ghufron]: Implementasi routes affiliate (TASK G1 & G3)
Route::prefix('affiliate')->name('affiliate.')->group(function () {
    Route::get('/register', [AffiliateController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/register', [AffiliateController::class, 'register'])->name('register');
    Route::get('/dashboard', [AffiliateController::class, 'dashboard'])->name('dashboard');
});
