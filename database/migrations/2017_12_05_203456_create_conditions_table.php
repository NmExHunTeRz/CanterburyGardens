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
            $table->float('high_humidity', 10,3)->nullable();
            $table->float('low_humidity', 10,3)->nullable();
            $table->float('high_moisture', 10,3)->nullable();
            $table->float('low_moisture', 10,3)->nullable();
            $table->float('high_lux', 10,3)->nullable();
            $table->float('low_lux', 10,3)->nullable();
            $table->float('gas', 10,3)->nullable();
            $table->float('high_temp', 10,3)->nullable();
            $table->float('low_temp', 10,3)->nullable();
            $table->float('winter_high_temp', 10,3)->nullable();
            $table->float('winter_low_temp', 10,3)->nullable();
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
