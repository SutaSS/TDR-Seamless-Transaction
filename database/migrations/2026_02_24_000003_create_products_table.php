<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TODO [PHASE 1 - Andika]: Buat tabel products sesuai Database.MD
     *
     * Kolom yang diperlukan:
     * - id (bigint, pk, increment)
     * - sku (varchar, unique, nullable)
     * - name (varchar)
     * - description (text, nullable)
     * - price (decimal 14,2)
     * - commission_rate (decimal 5,2, nullable)
     * - stock (int, nullable)
     * - is_active (boolean) default true
     * - timestamps
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            // TODO [PHASE 1 - Andika]: Definisikan kolom di sini
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
