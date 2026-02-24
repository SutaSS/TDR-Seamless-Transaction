<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TODO [PHASE 1 - Andika]: Buat tabel payments sesuai Database.MD
     *
     * Kolom yang diperlukan:
     * - id (bigint, pk, increment)
     * - order_id (bigint, FK → orders.id)
     * - gateway_provider (varchar) — e.g. 'midtrans'
     * - external_id (varchar, unique) — Midtrans order_id
     * - gateway_invoice_id (varchar, nullable)
     * - payment_method (varchar, nullable)
     * - invoice_url (text, nullable)
     * - amount (decimal 14,2)
     * - status (enum: pending, paid, expired, failed) default 'pending'
     * - signature_valid (boolean, nullable)
     * - raw_payload (text, nullable)
     * - webhook_received_at (timestamp, nullable)
     * - paid_at (timestamp, nullable)
     * - timestamps
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('gateway_provider');
            $table->string('external_id')->unique();
            $table->string('gateway_invoice_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('invoice_url')->nullable();
            $table->decimal('amount', 14, 2);
            $table->enum('status', ['pending', 'paid', 'expired', 'failed'])->default('pending');
            $table->boolean('signature_valid')->nullable();
            $table->text('raw_payload')->nullable();
            $table->timestamp('webhook_received_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
