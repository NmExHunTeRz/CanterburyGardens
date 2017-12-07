<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('site_id');
            $table->integer('high_humidity')->nullable();
            $table->integer('low_humidity')->nullable();
            $table->integer('high_moisture')->nullable();
            $table->integer('low_moisture')->nullable();
            $table->integer('high_lux')->nullable();
            $table->integer('low_lux')->nullable();
            $table->integer('high_gas')->nullable();
            $table->integer('low_gas')->nullable();
            $table->integer('high_temp')->nullable();
            $table->integer('low_temp')->nullable();
            $table->integer('winter_high_temp')->nullable();
            $table->integer('winter_low_temp')->nullable();
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
        Schema::dropIfExists('conditions');
    }
}
