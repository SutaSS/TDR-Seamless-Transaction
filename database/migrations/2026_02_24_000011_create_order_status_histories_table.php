<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TODO [PHASE 1 - Andika]: Buat tabel order_status_histories sesuai Database.MD
     *
     * Kolom yang diperlukan:
     * - id (bigint, pk, increment)
     * - order_id (bigint, FK → orders.id)
     * - old_status (enum: pending, processing, shipped, delivered, cancelled, nullable)
     * - new_status (enum: pending, processing, shipped, delivered, cancelled)
     * - changed_by_user_id (bigint, FK → users.id, nullable)
     * - note (text, nullable)
     * - created_at (timestamp)
     */
    public function up(): void
    {
        Schema::create('order_status_histories', function (Blueprint $table) {
            // TODO [PHASE 1 - Andika]: Definisikan kolom di sini
            // Note: no updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};
