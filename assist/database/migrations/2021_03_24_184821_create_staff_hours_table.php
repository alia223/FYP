<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('staffid')->unsigned();
            $table->integer('total_duration_worked_this_week');
            $table->timestamps();
            $table->foreign('staffid')->references('id')->on('users'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_hours');
    }
}
