<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveEverythingExceptIdFromStyles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('styles', function (Blueprint $table) {
            $table->dropColumn('header_image_max_height');
            $table->dropColumn('item_title_font_family');
            $table->dropColumn('item_title_display');
            $table->dropColumn('item_subtitle_color');
            $table->dropColumn('item_description_color');
            $table->dropColumn('item_title_font_weight');
            $table->dropColumn('item_subtitle_font_weight');
            $table->dropColumn('item_description_font_weight');
            $table->dropColumn('item_price_font_weight');
            $table->dropColumn('item_title_font_size');
            $table->dropColumn('item_subtitle_font_size');
            $table->dropColumn('item_description_font_size');
            $table->dropColumn('item_price_font_size');
            $table->dropColumn('item_price_width');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('styles', function (Blueprint $table) {
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
        });
    }
}
