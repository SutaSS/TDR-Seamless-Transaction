<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TODO [PHASE 1 - Andika]: Buat tabel orders sesuai Database.MD
     *
     * Kolom yang diperlukan:
     * - id (bigint, pk, increment)
     * - order_number (varchar, unique)
     * - customer_user_id (bigint, FK → users.id)
     * - affiliate_id (bigint, FK → affiliates.id, nullable)
     * - referral_click_id (bigint, FK → affiliate_referral_clicks.id, nullable)
     * - subtotal_amount (decimal 14,2)
     * - discount_amount (decimal 14,2) default 0
     * - total_amount (decimal 14,2)
     * - currency (varchar) default 'IDR'
     * - order_status (enum: pending, processing, shipped, delivered, cancelled) default 'pending'
     * - payment_status (enum: unpaid, paid, expired, failed) default 'unpaid'
     * - tracking_number (varchar, nullable)
     * - shipping_provider (varchar, nullable)
     * - customer_name (varchar, nullable)
     * - customer_phone (varchar, nullable)
     * - note (text, nullable)
     * - paid_at (timestamp, nullable)
     * - delivered_at (timestamp, nullable)
     * - timestamps
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            // TODO [PHASE 1 - Andika]: Definisikan kolom di sini
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
