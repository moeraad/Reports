<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJudgementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('judgements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('judge_court_id');
            $table->date('report_date');
            $table->date('arrival_date');
            $table->date('last_session');
            $table->date('judgement_date');
            $table->integer('judge_id');
            $table->integer('rule_number');
            $table->integer('speciality_id');
            $table->integer('status_id');
            $table->integer('judgment_type_id');
            $table->integer('sessions_count');
            $table->string('notes');
            $table->string('decision_source');
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
        Schema::drop('judgements');
    }
}
