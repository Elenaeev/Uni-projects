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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->unsignedBigInteger('customer_user_id')->references('user_id')->on('users');
            $table->dateTime('creation_date');
            $table->dateTime('close_date')->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->enum('status', ['cart', 'in process', 'canceled', 'delivered'])->default('cart');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
