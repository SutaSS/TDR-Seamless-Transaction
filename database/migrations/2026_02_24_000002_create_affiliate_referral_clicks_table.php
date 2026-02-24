<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TODO [PHASE 1 - Andika]: Buat tabel affiliate_referral_clicks sesuai Database.MD
     *
     * Kolom yang diperlukan:
     * - id (bigint, pk, increment)
     * - affiliate_id (bigint, FK → affiliates.id)
     * - referral_code_snapshot (varchar)
     * - session_key (varchar, nullable)
     * - anonymized_ip (varchar, nullable)
     * - user_agent (text, nullable)
     * - landing_url (text, nullable)
     * - is_attributed (boolean) default false
     * - created_at (timestamp)
     * - expires_at (timestamp, nullable)
     *
     * Note: Click tracking per referral link (window 30 hari)
     */
    public function up(): void
    {
        Schema::create('affiliate_referral_clicks', function (Blueprint $table) {
            // TODO [PHASE 1 - Andika]: Definisikan kolom di sini
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_referral_clicks');
    }
};
