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
            $table->id();
            $table->foreignId('restaurant_id')->nullable()->constrained('restaurant_profiles')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customer_profiles')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('driver_profiles')->onDelete('cascade');
            $table->enum('status', ['pending', 'preparing', 'delivering', 'completed', 'cancelled'])->default('pending')->nullable();
            $table->decimal('total_amount', 6, 2)->nullable();
            $table->boolean('payment_status',)->nullable()->default(false);
            $table->string('remark')->nullable();
            $table->enum('order_type', ['dine-in', 'delivery'])->nullable();
            $table->timestamps();
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
