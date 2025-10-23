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
        Schema::create('event_products', function (Blueprint $table) {
            $table->id('event_product_id');
            $table->unsignedBigInteger('event_id')->references('event_id')->on('events');
            $table->unsignedBigInteger('product_id')->references('product_id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_products');
    }
};
