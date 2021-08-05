<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonthlyReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('judge_court_id');
            $table->integer('speciality_id');
            $table->integer('judge_id');
            $table->integer('arriving');
            $table->integer('eliminatedArrival');
            $table->integer('rotated');
            $table->integer('casesOnSchedule');
            $table->integer('totalSeparated');
            $table->integer('totalCases');
            $table->integer('remainedCases');
            $table->integer('others');
            $table->date('date');
            $table->integer('created_by');
            $table->integer('modified_by');
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
        Schema::drop('monthly_reports');
    }
}
