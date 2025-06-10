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
        Schema::create('driver_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('text')->nullable();
            $table->string('link_to')->nullable();
            $table->timestamps();
        });

        Schema::create('driver_section_buttons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('driver_sections')->onDelete('cascade');
            $table->string('img_src')->nullable();
            $table->string('name')->nullable();
            $table->text('text')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
        });

        Schema::create('driver_section_button_descs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('button_id')->constrained('driver_section_buttons')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('text')->nullable();
            $table->string('svg')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_sections');
        Schema::dropIfExists('driver_section_buttons');
        Schema::dropIfExists('driver_section_button_descs');
    }
};
