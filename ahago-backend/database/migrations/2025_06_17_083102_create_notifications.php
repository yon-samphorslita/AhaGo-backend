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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customer_profiles')->onDelete('cascade');
            $table->foreignId('restaurant_id')->nullable()->constrained('restaurant_profiles')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('driver_profiles')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('admin_profiles')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
