<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookedPupilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booked_pupils', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('booking_id')->unsigned();
            $table->integer('event_id');
            $table->bigInteger('parent_id')->unsigned();
            $table->bigInteger('pupil_id')->unsigned();
            $table->date('booking_date');
            $table->integer('booking_day');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('checked_in')->nullable();
            $table->time('checked_out')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('parent_id')->references('id')->on('users'); 
            $table->foreign('booking_id')->references('id')->on('bookings'); 
            $table->foreign('pupil_id')->references('id')->on('pupils'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booked_pupils');
    }
}
