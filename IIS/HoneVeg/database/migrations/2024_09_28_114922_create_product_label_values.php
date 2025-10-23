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
        Schema::create('product_label_values', function (Blueprint $table) {
            $table->id('product_label_values_id');
            $table->unsignedBigInteger('product_id')->references('product_id')->on('products');
            $table->unsignedBigInteger('label_id')->references('label_id')->on('labels');
            $table->string('label_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_label_values');
    }
};
