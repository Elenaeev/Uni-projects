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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->unsignedBigInteger('seller_user_id')->references('user_id')->on('users');
            $table->unsignedBigInteger('category_id')->references('category_id')->on('categories');
            $table->float('price');
            $table->text('description')->nullable();
            $table->integer('available_amount');
            $table->float('total_rating');
            $table->string('name');
            #$table
        });
    } 

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
