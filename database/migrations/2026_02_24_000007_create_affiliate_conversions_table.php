<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TODO [PHASE 2 - Ghufron]: Buat tabel affiliate_conversions sesuai Database.MD
     *
     * Kolom yang diperlukan:
     * - id (bigint, pk, increment)
     * - affiliate_id (bigint, FK → affiliates.id)
     * - order_id (bigint, unique, FK → orders.id)
     * - referral_click_id (bigint, FK → affiliate_referral_clicks.id, nullable)
     * - commission_rate (decimal 5,2)
     * - commission_amount (decimal 14,2)
     * - is_self_referral (boolean) default false
     * - status (enum: pending, approved, paid, rejected) default 'pending'
     * - approved_at (timestamp, nullable)
     * - paid_at (timestamp, nullable)
     * - timestamps
     */
    public function up(): void
    {
        Schema::create('affiliate_conversions', function (Blueprint $table) {
            // TODO [PHASE 2 - Ghufron]: Definisikan kolom di sini
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_conversions');
    }
};
