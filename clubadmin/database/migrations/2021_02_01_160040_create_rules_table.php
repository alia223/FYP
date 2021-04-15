<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('brand_logo')->nullable();
            $table->string('brand_colour');
            $table->string('text_colour');
            $table->string('club_start');
            $table->string('club_end');
            $table->integer('club_duration_step');
            $table->integer('booking_interval');
            $table->integer('student_ratio');
            $table->timestamps();
        });

        // Insert some stuff
        DB::table('rules')->insert(
            array(
                'brand_logo' => 'brand_logo.png',
                'brand_colour' => '#8400ff',
                'text_colour' => '#ffffff',
                'club_start' => '15:30:00',
                'club_end' => '19:30:00',
                'club_duration_step' => '30',
                'booking_interval' => '0',
                'student_ratio' => '6'
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
        Schema::dropIfExists('rules');
    }
}
