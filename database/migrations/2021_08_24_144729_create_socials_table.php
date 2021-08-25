<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('socials', function (Blueprint $table) {
            $table->id();

            $table->string('tripadvisor_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('foursquare_url')->nullable();
            $table->string('google_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('instagram_url')->nullable();

            $table->unsignedBigInteger('restaurant_id')->unique();
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('CASCADE');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('socials');
    }
}
