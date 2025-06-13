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
        Schema::create('food_item_reivews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer_profiles')->onDelete('cascade');
            $table->foreignId('food_item_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_item_reivews');
    }
};
