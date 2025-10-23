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
        Schema::create('seller_orders', function (Blueprint $table) {
            $table->id('seller_order_id');
            $table->unsignedBigInteger('seller_id')->references('user_id')->on('users');
            $table->unsignedBigInteger('order_id')->references('order_id')->on('orders');
            $table->enum('status', ['accepted', 'canceled', 'delivered'])->default('accepted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_orders');
    }
};
