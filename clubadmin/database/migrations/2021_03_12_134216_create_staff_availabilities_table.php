<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_availabilities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('staffid')->unsigned();
            $table->integer('day');
            $table->time('available_from')->nullable();
            $table->time('available_until')->nullable();
            $table->integer('available_for')->nullable();
            $table->integer('max_hours')->nullable();
            $table->integer('total_duration_worked_this_day')->default(0);
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
        Schema::dropIfExists('staff_availabilities');
    }
}
