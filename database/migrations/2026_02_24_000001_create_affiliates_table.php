<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TODO [PHASE 1 - Andika]: Buat tabel affiliates sesuai Database.MD
     *
     * Kolom yang diperlukan:
     * - id (bigint, pk, increment)
     * - user_id (bigint, FK → users.id, unique)
     * - referral_code (varchar, unique)
     * - status (enum: pending, approved, rejected) default 'approved'
     * - commission_rate (decimal 5,2) default 10.00
     * - payout_method (varchar, nullable)
     * - payout_account_name (varchar, nullable)
     * - payout_account_number (varchar, nullable)
     * - total_clicks (int) default 0
     * - total_conversions (int) default 0
     * - total_commission_amount (decimal 14,2) default 0
     * - timestamps
     */
    public function up(): void
    {
        Schema::create('affiliates', function (Blueprint $table) {
            // TODO [PHASE 1 - Andika]: Definisikan kolom di sini
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
