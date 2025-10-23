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
        Schema::create('design_labels', function (Blueprint $table) {
            $table->id('design_label_id');
            $table->unsignedBigInteger('design_id')->references('design_id')->on('change_categories_designs');
            $table->string('name');
            $table->enum('type', ['number', 'text'])->default('text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_labels');
    }
};
