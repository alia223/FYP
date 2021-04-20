<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePupilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pupils', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('parent_id')->unsigned();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->string('other_dietary_requirements')->nullable();
            $table->string('food_arrangement');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('parent_id')->references('id')->on('users'); 
        });

        DB::table('pupils')->insert(
            array(
                'parent_id' => '4',
                'first_name' => 'Child One',
                'last_name' => 'Parent One',
                'date_of_birth' => '2021-04-03',
                'food_arrangement' => 'None'
            )
        );

        DB::table('pupils')->insert(
            array(
                'parent_id' => '4',
                'first_name' => 'Child Two',
                'last_name' => 'Parent One',
                'date_of_birth' => '2021-04-03',
                'food_arrangement' => 'None'
            )
        );

        DB::table('pupils')->insert(
            array(
                'parent_id' => '4',
                'first_name' => 'Child Three',
                'last_name' => 'Parent One',
                'date_of_birth' => '2021-04-03',
                'food_arrangement' => 'None'
            )
        );

        DB::table('pupils')->insert(
            array(
                'parent_id' => '5',
                'first_name' => 'Child One',
                'last_name' => 'Parent Two',
                'date_of_birth' => '2021-04-03',
                'food_arrangement' => 'None'
            )
        );

        DB::table('pupils')->insert(
            array(
                'parent_id' => '5',
                'first_name' => 'Child Two',
                'last_name' => 'Parent Two',
                'date_of_birth' => '2021-04-03',
                'food_arrangement' => 'None'
            )
        );

        DB::table('pupils')->insert(
            array(
                'parent_id' => '5',
                'first_name' => 'Child Three',
                'last_name' => 'Parent Two',
                'date_of_birth' => '2021-04-03',
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
        Schema::dropIfExists('pupils');
    }
}
