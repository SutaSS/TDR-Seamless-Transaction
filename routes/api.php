<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| TODO [PHASE 1 - Andika]: Daftarkan route webhook payment Midtrans
| TODO [PHASE 2 - Ghufron]: Tambahkan route API affiliate jika diperlukan
|
*/

// TODO [PHASE 1 - Andika]: Webhook endpoint Midtrans
// POST /api/webhook/payment
Route::post('/webhook/payment', [WebhookController::class, 'handlePayment'])
    ->name('webhook.payment');
    // TODO [PHASE 1 - Andika]: Pastikan route ini EXEMPT dari CSRF middleware
