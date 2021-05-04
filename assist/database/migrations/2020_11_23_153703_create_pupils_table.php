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
            $table->string('food_arrangement');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('parent_id')->references('id')->on('users'); 
        });

        DB::table('pupils')->insert(array('parent_id' => '4', 'first_name' => 'Caitlyn', 'last_name' => 'Weldon', 'date_of_birth' => '2018-01-01', 'food_arrangement' => 'None'));
        DB::table('pupils')->insert(array('parent_id' => '4', 'first_name' => 'Tamsin', 'last_name' => 'Cosmo', 'date_of_birth' => '2018-02=02', 'food_arrangement' => 'None'));
        DB::table('pupils')->insert(array('parent_id' => '4', 'first_name' => 'Mitchell', 'last_name' => 'Gordy', 'date_of_birth' => '2018-03-03', 'food_arrangement' => 'None'));
        DB::table('pupils')->insert(array('parent_id' => '4', 'first_name' => 'Scottie', 'last_name' => 'Pippen', 'date_of_birth' => '2018-02=02', 'food_arrangement' => 'None'));
        DB::table('pupils')->insert(array('parent_id' => '4', 'first_name' => 'Anthony', 'last_name' => 'Davis', 'date_of_birth' => '2018-03-03', 'food_arrangement' => 'None'));
        DB::table('pupils')->insert(array('parent_id' => '5', 'first_name' => 'Kimball', 'last_name' => 'Reed', 'date_of_birth' => '2018-04-04', 'food_arrangement' => 'None'));
        DB::table('pupils')->insert(array('parent_id' => '5', 'first_name' => 'Keely', 'last_name' => 'Rory', 'date_of_birth' => '2018-05-05', 'food_arrangement' => 'None'));
        DB::table('pupils')->insert(array('parent_id' => '5','first_name' => 'Scot','last_name' => 'Margery','date_of_birth' => '2018-06-06','food_arrangement' => 'None'));
        DB::table('pupils')->insert(array('parent_id' => '5', 'first_name' => 'Michael', 'last_name' => 'Jordan', 'date_of_birth' => '2018-05-05', 'food_arrangement' => 'None'));
        DB::table('pupils')->insert(array('parent_id' => '5','first_name' => 'LeBron','last_name' => 'James','date_of_birth' => '2018-06-06','food_arrangement' => 'None'));


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
