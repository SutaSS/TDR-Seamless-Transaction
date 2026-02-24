<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TODO [PHASE 1 - Andika]: Buat tabel order_items sesuai Database.MD
     *
     * Kolom yang diperlukan:
     * - id (bigint, pk, increment)
     * - order_id (bigint, FK → orders.id)
     * - product_id (bigint, FK → products.id, nullable)
     * - product_name_snapshot (varchar) — snapshot nama produk saat order
     * - qty (int)
     * - unit_price (decimal 14,2)
     * - line_total (decimal 14,2)
     * - created_at (timestamp)
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            // TODO [PHASE 1 - Andika]: Definisikan kolom di sini
            // Note: no updated_at (sesuai schema)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
