<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TODO [PHASE 1 - Andika]: Buat tabel settings_audit_logs sesuai Database.MD
     *
     * Kolom yang diperlukan:
     * - id (bigint, pk, increment)
     * - actor_user_id (bigint, FK → users.id)
     * - config_key (varchar)
     * - old_value_masked (text, nullable) — nilai lama (disensor jika is_secret)
     * - new_value_masked (text, nullable) — nilai baru (disensor jika is_secret)
     * - created_at (timestamp)
     */
    public function up(): void
    {
        Schema::create('settings_audit_logs', function (Blueprint $table) {
            // TODO [PHASE 1 - Andika]: Definisikan kolom di sini
            // Note: no updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings_audit_logs');
    }
};
