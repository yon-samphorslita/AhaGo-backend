<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFoodItemIdToReviewsTable extends Migration
{
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('food_item_id')->after('id');

            // Optionally add foreign key if you want referential integrity
            // $table->foreign('food_item_id')->references('id')->on('food_items')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            // If you added foreign key uncomment the following line
            // $table->dropForeign(['food_item_id']);
            $table->dropColumn('food_item_id');
        });
    }
}
