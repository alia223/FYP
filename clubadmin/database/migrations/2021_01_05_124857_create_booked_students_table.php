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
            $table->id();
            $table->bigInteger('parentid')->unsigned();
            $table->bigInteger('bookingid')->unsigned();
            $table->bigInteger('studentid')->unsigned();
            $table->bigInteger('roomid')->unsigned();
            $table->integer('attendance')->default('0');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('parentid')->references('id')->on('users'); 
            $table->foreign('bookingid')->references('id')->on('bookings'); 
            $table->foreign('studentid')->references('id')->on('students'); 
            $table->foreign('roomid')->references('id')->on('rooms'); 
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
