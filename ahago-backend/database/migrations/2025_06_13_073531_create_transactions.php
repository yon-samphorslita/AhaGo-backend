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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer_profiles')->onDelete('cascade')->nullable();
            $table->foreignId('restaurant_id')->constrained('restaurant_profiles')->onDelete('cascade')->nullable();
            $table->foreignId('order_id')->constrained()->nullable()->onDelete('cascade');
            $table->enum('payment', ['cash', 'card'])->nullable()->default('cash');
            $table->integer('amount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
