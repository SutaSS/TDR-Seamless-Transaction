<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// POST /api/webhook/payment — Midtrans webhook endpoint
// Exempt dari CSRF secara otomatis karena ada di routes/api.php
Route::post('/webhook/payment', [WebhookController::class, 'handlePayment'])
    ->name('webhook.payment');
