<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TODO [PHASE 1 - Andika]: Buat tabel system_configs sesuai Database.MD
     *
     * Kolom yang diperlukan:
     * - id (bigint, pk, increment)
     * - config_key (varchar, unique)
     * - config_value (text)
     * - is_secret (boolean) default false — jika true, jangan tampilkan di UI
     * - updated_by_user_id (bigint, FK → users.id, nullable)
     * - timestamps
     */
    public function up(): void
    {
        Schema::create('system_configs', function (Blueprint $table) {
            // TODO [PHASE 1 - Andika]: Definisikan kolom di sini
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_configs');
    }
};
