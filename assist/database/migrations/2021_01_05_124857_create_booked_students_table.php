<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookedStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booked_students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('parentid')->unsigned();
            $table->bigInteger('bookingid')->unsigned();
            $table->integer('eventid');
            $table->bigInteger('studentid')->unsigned();
            $table->date('booking_date');
            $table->integer('booking_day');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('checked_in')->nullable();
            $table->time('checked_out')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('parentid')->references('id')->on('users'); 
            $table->foreign('bookingid')->references('id')->on('bookings'); 
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
        Schema::dropIfExists('booked_students');
    }
}
