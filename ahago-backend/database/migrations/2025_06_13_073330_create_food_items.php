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
        Schema::create('food_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->unique()->constrained('restaurant_profiles')->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->unique()->onDelete('cascade')->nullable();
            $table->string('name')->nullable();
            $table->decimal('price', 4, 2)->default(00.00)->nullable();
            $table->string('description')->nullable();
            $table->boolean('available')->default(true)->nullable();
            $table->integer('discount')->nullable();
            $table->string('img_url')->nullable();
            $table->tinyInteger('rating')->unsigned()->nullable();
            $table->integer('sold')->nullable();
            $table->boolean('favourite')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_items');
    }
};
