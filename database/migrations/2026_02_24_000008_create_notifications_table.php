<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TODO [PHASE 3 - Syahru]: Buat tabel notifications sesuai Database.MD
     *
     * Kolom yang diperlukan:
     * - id (bigint, pk, increment)
     * - user_id (bigint, FK → users.id)
     * - order_id (bigint, FK → orders.id, nullable)
     * - conversion_id (bigint, FK → affiliate_conversions.id, nullable)
     * - event_type (varchar)
     * - channel (enum: telegram) default 'telegram'
     * - recipient_chat_id_snapshot (varchar, nullable)
     * - template_key (varchar, nullable)
     * - message_body (text)
     * - provider_message_id (varchar, nullable)
     * - status (enum: queued, sent, failed) default 'queued'
     * - retry_count (int) default 0
     * - last_error (text, nullable)
     * - sent_at (timestamp, nullable)
     * - timestamps
     *
     * Note: Status 'sent' = accepted by Telegram API. No delivery receipt required in MVP
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            // TODO [PHASE 3 - Syahru]: Definisikan kolom di sini
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
