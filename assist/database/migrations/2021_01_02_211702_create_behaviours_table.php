<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBehavioursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('behaviours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('staffid')->unsigned();
            $table->bigInteger('studentid')->unsigned();
            $table->date('date');
            $table->string('stars');
            $table->string('comment');
            $table->timestamps();
            $table->foreign('staffid')->references('id')->on('users'); 
            $table->foreign('studentid')->references('id')->on('students');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('behaviours');
    }
}
