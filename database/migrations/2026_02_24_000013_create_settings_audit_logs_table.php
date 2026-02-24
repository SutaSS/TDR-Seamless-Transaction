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
            $table->id();
            $table->foreignId('actor_user_id')->constrained('users');
            $table->string('config_key');
            $table->text('old_value_masked')->nullable();
            $table->text('new_value_masked')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings_audit_logs');
    }
};
