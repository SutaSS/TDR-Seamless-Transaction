<?php

use App\Http\Controllers\TelegramBotController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// POST /api/webhook/payment — Midtrans webhook endpoint
// Exempt dari CSRF secara otomatis karena ada di routes/api.php
Route::post('/webhook/payment', [WebhookController::class, 'handlePayment'])
    ->name('webhook.payment');

// POST /api/telegram/bot — Handle incoming Telegram bot messages (e.g. /start)
Route::post('/telegram/bot', [TelegramBotController::class, 'handle'])
    ->name('telegram.bot');
