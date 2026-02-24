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
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_user_id')->constrained('users');
            $table->foreignId('affiliate_id')->nullable()->constrained('affiliates')->nullOnDelete();
            $table->unsignedBigInteger('referral_click_id')->nullable();
            $table->foreign('referral_click_id')->references('id')->on('affiliate_referral_clicks')->nullOnDelete();
            $table->decimal('subtotal_amount', 14, 2);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2);
            $table->string('currency', 10)->default('IDR');
            $table->enum('order_status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'expired', 'failed'])->default('unpaid');
            $table->string('tracking_number')->nullable();
            $table->string('shipping_provider')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
