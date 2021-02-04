<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parentid')->unsigned();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->string('dietary_requirements');
            $table->string('other_dietary_requirements')->nullable();
            $table->string('food_arrangement');
            $table->timestamps();
            $table->foreign('parentid')->references('id')->on('users'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
