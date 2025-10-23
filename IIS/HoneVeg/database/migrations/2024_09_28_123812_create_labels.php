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
        Schema::create('labels', function (Blueprint $table) {
            $table->id('label_id')->references('label_id')->on('change_categories_design_labels')->references('label_id')->on('product_label_values');
            $table->unsignedBigInteger('category_id')->references('category_id')->on('categories');
            $table->string('name');
            $table->enum('type', ['number', 'text'])->default('text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labels');
    }
};
