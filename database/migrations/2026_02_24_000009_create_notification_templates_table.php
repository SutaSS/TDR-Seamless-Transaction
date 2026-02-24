<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TODO [PHASE 3 - Syahru]: Buat tabel notification_templates sesuai Database.MD
     *
     * Kolom yang diperlukan:
     * - id (bigint, pk, increment)
     * - template_key (varchar, unique)
     * - channel (enum: telegram) default 'telegram'
     * - event_type (varchar)
     * - title (varchar, nullable)
     * - body_template (text) — bisa gunakan placeholder seperti {{name}}, {{amount}}
     * - is_active (boolean) default true
     * - created_by_user_id (bigint, FK → users.id, nullable)
     * - timestamps
     */
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            // TODO [PHASE 3 - Syahru]: Definisikan kolom di sini
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
