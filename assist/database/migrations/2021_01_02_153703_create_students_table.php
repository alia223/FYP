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
            $table->bigIncrements('id');
            $table->bigInteger('parentid')->unsigned();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->string('dietary_requirements');
            $table->string('other_dietary_requirements')->nullable();
            $table->string('food_arrangement');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('parentid')->references('id')->on('users'); 
        });

        DB::table('students')->insert(
            array(
                'parentid' => '4',
                'first_name' => 'Child One',
                'last_name' => 'Parent One',
                'date_of_birth' => '2021-04-03',
                'dietary_requirements' => 'None',
                'food_arrangement' => 'None'
            )
        );

        DB::table('students')->insert(
            array(
                'parentid' => '4',
                'first_name' => 'Child Two',
                'last_name' => 'Parent One',
                'date_of_birth' => '2021-04-03',
                'dietary_requirements' => 'None',
                'food_arrangement' => 'None'
            )
        );

        DB::table('students')->insert(
            array(
                'parentid' => '4',
                'first_name' => 'Child Three',
                'last_name' => 'Parent One',
                'date_of_birth' => '2021-04-03',
                'dietary_requirements' => 'None',
                'food_arrangement' => 'None'
            )
        );

        DB::table('students')->insert(
            array(
                'parentid' => '5',
                'first_name' => 'Child One',
                'last_name' => 'Parent Two',
                'date_of_birth' => '2021-04-03',
                'dietary_requirements' => 'None',
                'food_arrangement' => 'None'
            )
        );

        DB::table('students')->insert(
            array(
                'parentid' => '5',
                'first_name' => 'Child Two',
                'last_name' => 'Parent Two',
                'date_of_birth' => '2021-04-03',
                'dietary_requirements' => 'None',
                'food_arrangement' => 'None'
            )
        );

        DB::table('students')->insert(
            array(
                'parentid' => '5',
                'first_name' => 'Child Three',
                'last_name' => 'Parent Two',
                'date_of_birth' => '2021-04-03',
                'dietary_requirements' => 'None',
                'food_arrangement' => 'None'
            )
        );
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
