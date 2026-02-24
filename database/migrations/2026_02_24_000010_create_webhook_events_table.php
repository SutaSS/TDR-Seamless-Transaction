<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TODO [PHASE 1 - Andika]: Buat tabel webhook_events sesuai Database.MD
     *
     * Kolom yang diperlukan:
     * - id (bigint, pk, increment)
     * - source (varchar) — e.g. 'midtrans'
     * - event_id (varchar, nullable)
     * - external_id (varchar, nullable) — referensi ke payments.external_id
     * - payload_hash (varchar, nullable) — untuk deteksi duplikat
     * - signature_valid (boolean) default false
     * - process_status (enum: received, processed, duplicate, invalid_signature, failed) default 'received'
     * - error_message (text, nullable)
     * - raw_payload (text)
     * - received_at (timestamp)
     * - processed_at (timestamp, nullable)
     *
     * Note: No 'id' auto-increment tidak perlu FK ke payments karena bisa hadir sebelum payment record
     */
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->string('event_id')->nullable();
            $table->string('external_id')->nullable()->index();
            $table->string('payload_hash')->nullable();
            $table->boolean('signature_valid')->default(false);
            $table->enum('process_status', ['received', 'processed', 'duplicate', 'invalid_signature', 'failed'])->default('received');
            $table->text('error_message')->nullable();
            $table->text('raw_payload');
            $table->timestamp('received_at');
            $table->timestamp('processed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
