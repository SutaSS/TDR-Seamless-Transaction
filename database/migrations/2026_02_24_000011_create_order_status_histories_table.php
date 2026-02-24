<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TODO [PHASE 1 - Andika]: Buat tabel order_status_histories sesuai Database.MD
     *
     * Kolom yang diperlukan:
     * - id (bigint, pk, increment)
     * - order_id (bigint, FK → orders.id)
     * - old_status (enum: pending, processing, shipped, delivered, cancelled, nullable)
     * - new_status (enum: pending, processing, shipped, delivered, cancelled)
     * - changed_by_user_id (bigint, FK → users.id, nullable)
     * - note (text, nullable)
     * - created_at (timestamp)
     */
    public function up(): void
    {
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->enum('old_status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->nullable();
            $table->enum('new_status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled']);
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->foreign('changed_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};
