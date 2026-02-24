<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    /**
     * TODO [PHASE 3 - Syahru]: Implementasi sesuai TASK S1
     *
     * Gunakan TELEGRAM_BOT_TOKEN dari .env
     * Return true jika berhasil, false jika gagal
     * No retry di layer ini (retry ditangani Job)
     */

    // TODO [PHASE 3 - Syahru]: Inject bot token dari config/services.php
    // protected string $botToken;

    public function __construct()
    {
        // TODO [PHASE 3 - Syahru]: $this->botToken = config('services.telegram.bot_token');
    }

    /**
     * Send a message to a Telegram chat.
     *
     * @param  string  $chatId   Telegram chat_id penerima
     * @param  string  $message  Pesan yang akan dikirim
     * @return bool
     *
     * TODO [PHASE 3 - Syahru]: Implementasi call ke Telegram Bot API
     * Endpoint: https://api.telegram.org/bot{TOKEN}/sendMessage
     */
    public function sendMessage(string $chatId, string $message): bool
    {
        // TODO [PHASE 3 - Syahru]: Implementasi HTTP call ke Telegram di sini
        return false;
    }
}
