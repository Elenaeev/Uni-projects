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
        Schema::create('order_product_lists', function (Blueprint $table) {
            $table->id('order_product_list_id');
            $table->unsignedBigInteger('product_id')->references('product_id')->on('products');
            $table->unsignedBigInteger('order_id')->references('order_id')->on('orders');
            $table->unsignedBigInteger('product_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product_lists');
    }
};
