<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('telephone');
            $table->string('mobile');
            $table->datetime('email_verified_at')->nullable();
            $table->boolean('admin')->default(0);
            $table->boolean('clubstaff')->default(0);
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert some stuff
        DB::table('users')->insert(
            array(
                'name' => 'Asfand',
                'last_name' => 'Ali',
                'email' => 'asfand.y.ali99@gmail.com',
                'telephone' => '0121 123 4567',
                'mobile' => '07123456789',
                'admin' => '1',
                'clubstaff' => '0',
                'password' => Hash::make('Asfand0223')
            )
        );

        // Insert some stuff
        DB::table('users')->insert(
            array(
                'name' => 'Staff',
                'last_name' => 'One',
                'email' => 'staffone@gmail.com',
                'telephone' => '0121 123 4567',
                'mobile' => '07123456789',
                'admin' => '0',
                'clubstaff' => '1',
                'password' => Hash::make('Asfand0223')
            )
        );

        DB::table('users')->insert(
            array(
                'name' => 'Staff',
                'last_name' => 'Two',
                'email' => 'stafftwo@gmail.com',
                'telephone' => '0121 123 4567',
                'mobile' => '07123456789',
                'admin' => '0',
                'clubstaff' => '1',
                'password' => Hash::make('Asfand0223')
            )
        );

        DB::table('users')->insert(
            array(
                'name' => 'Parent',
                'last_name' => 'One',
                'email' => 'parentone@gmail.com',
                'telephone' => '0121 123 4567',
                'mobile' => '07123456789',
                'admin' => '0',
                'clubstaff' => '0',
                'password' => Hash::make('Asfand0223')
            )
        );

        DB::table('users')->insert(
            array(
                'name' => 'Parent',
                'last_name' => 'Two',
                'email' => 'parenttwo@gmail.com',
                'telephone' => '0121 123 4567',
                'mobile' => '07123456789',
                'admin' => '0',
                'clubstaff' => '0',
                'password' => Hash::make('Asfand0223')
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
        Schema::dropIfExists('users');
    }
}
