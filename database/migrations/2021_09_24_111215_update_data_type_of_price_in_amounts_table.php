<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDataTypeOfPriceInAmountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('amounts', function (Blueprint $table) {
            $table -> decimal('price', $total = 6, $places = 2) -> nullable() -> change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('amounts', function (Blueprint $table) {
            $table -> integer('price')-> nullable() -> change();
        });
    }
}
