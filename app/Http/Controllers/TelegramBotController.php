<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Handles incoming Telegram bot messages.
 *
 * Register webhook:
 *   https://api.telegram.org/bot{TOKEN}/setWebhook?url=https://your-domain/api/telegram/bot
 */
class TelegramBotController extends Controller
{
    public function __construct(private TelegramService $telegram) {}

    /**
     * POST /api/telegram/bot — Telegram sends all incoming messages here.
     */
    public function handle(Request $request): Response
    {
        $body = $request->all();

        // Only handle regular messages (not edited, callback queries, etc.)
        $message = $body['message'] ?? null;
        if (! $message) {
            return response('', 200);
        }

        $chatId = $message['chat']['id'] ?? null;
        $text   = trim($message['text'] ?? '');
        $from   = $message['from'] ?? [];
        $name   = $from['first_name'] ?? 'Pengguna';

        if (! $chatId) {
            return response('', 200);
        }

        // Handle /start command
        if (str_starts_with($text, '/start')) {
            $msg = "👋 Halo *{$name}*!\n\n"
                 . "Selamat datang di bot *TDR HPZ Store*. 🏍\n\n"
                 . "Untuk menghubungkan akun Anda dan menerima notifikasi pesanan, "
                 . "salin *Chat ID* berikut dan tempel di website:\n\n"
                 . "🔑 Chat ID Anda: `{$chatId}`\n\n"
                 . "Buka profil Anda di website, masukkan angka di atas pada kolom *Telegram Chat ID*, "
                 . "lalu simpan. Setelah itu Anda akan menerima notifikasi otomatis untuk setiap update pesanan! ✅";

            $this->telegram->sendMessage((string) $chatId, $msg);
            return response('', 200);
        }

        // Default reply for unknown commands
        $this->telegram->sendMessage(
            (string) $chatId,
            "Halo! Kirim /start untuk mendapatkan *Chat ID* Anda dan menghubungkan akun TDR HPZ Store. 🏍"
        );

        return response('', 200);
    }
}
