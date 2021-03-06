<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJudgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('judges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('placenaisssance');
            $table->string('residence');
            $table->string('dateenservice');
            $table->string('dateenjuridiction');
            $table->string('datepromotion');
            $table->integer('degree');
            $table->integer('timesChanged');
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
        Schema::drop('judges');
    }
}
