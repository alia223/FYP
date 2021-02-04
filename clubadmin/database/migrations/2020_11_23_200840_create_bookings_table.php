<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('userid')->unsigned();
            $table->string('name', 255);
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('duration');
            $table->bigInteger('roomid')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('userid')->references('id')->on('users'); 
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
        Schema::dropIfExists('bookings');
    }
}
