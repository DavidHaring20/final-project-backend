<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStylesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('styles', function (Blueprint $table) {
            $table->id();

            $table->string('header_image_max_height');
            $table->string('item_title_font_family');
            $table->string('item_title_display');
            $table->string('item_subtitle_color');
            $table->string('item_description_color');
            $table->string('item_title_font_weight');
            $table->string('item_subtitle_font_weight');
            $table->string('item_description_font_weight');
            $table->string('item_price_font_weight');
            $table->string('item_title_font_size');
            $table->string('item_subtitle_font_size');
            $table->string('item_description_font_size');
            $table->string('item_price_font_size');
            $table->string('item_price_width');

            $table->unsignedBigInteger('restaurant_id');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('styles');
    }
}
