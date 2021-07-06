<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmountTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amount_translations', function (Blueprint $table) {
            $table->id();

            $table->string('language_code');
            $table->boolean('is_default');
            $table->string('description')->nullable();

            $table->unsignedBigInteger('amount_id');
            $table->foreign('amount_id')->references('id')->on('amounts')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('amount_translations');
    }
}
