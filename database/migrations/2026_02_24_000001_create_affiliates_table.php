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
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('referral_code')->unique();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->decimal('commission_rate', 5, 2)->default(10.00);
            $table->string('payout_method')->nullable();
            $table->string('payout_account_name')->nullable();
            $table->string('payout_account_number')->nullable();
            $table->unsignedInteger('total_clicks')->default(0);
            $table->unsignedInteger('total_conversions')->default(0);
            $table->decimal('total_commission_amount', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
