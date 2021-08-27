<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeyAndAddValueAndAddRestaurantIdToStyles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('styles', function (Blueprint $table) {
            $table -> string('key');
            $table -> string('value')->nullable();
            
            $table -> unsignedBigInteger('restaurant_id');
            $table -> foreign('restaurant_id') -> references('id') -> on('restaurants') -> onDelete('CASCADE');
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
            $table -> dropColumn('key');
            $table -> dropColumn('value');
            $table -> dropColumn('restaurant_id');
        });
    }
}
