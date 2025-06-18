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
        Schema::table('users', function (Blueprint $table) {
            $table->string('address')->nullable()->after('remember_token');
            $table->string('phone_number')->nullable()->after('address');
            $table->string('img_src')->nullable()->after('phone_number');
            $table->enum('role', ['admin', 'customer', 'driver', 'restaurant'])->default('customer')->after('img_src');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
             $table->dropColumn(['address', 'phone_number', 'img_src', 'role']);
        });
    }
};
