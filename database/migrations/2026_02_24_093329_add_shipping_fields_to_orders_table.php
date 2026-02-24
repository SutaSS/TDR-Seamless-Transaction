<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_address')->nullable()->after('customer_phone');
            $table->string('shipping_city')->nullable()->after('shipping_address');
            $table->string('shipping_province')->nullable()->after('shipping_city');
            $table->string('shipping_postal_code', 10)->nullable()->after('shipping_province');
            $table->string('shipping_courier')->nullable()->after('shipping_postal_code');
            $table->unsignedInteger('shipping_cost')->default(0)->after('shipping_courier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_address', 'shipping_city', 'shipping_province',
                'shipping_postal_code', 'shipping_courier', 'shipping_cost',
            ]);
        });
    }
};
