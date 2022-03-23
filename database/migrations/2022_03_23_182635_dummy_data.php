<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DummyData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('user')) {
            $user_id = \DB::table('user')->insertGetId(
                array(
                    'username' => 'admin',
                    'password' => '123456'
                )
            );

            if (Schema::hasTable('artwork')) {
                \DB::table('artwork')->insert(
                    array(
                        'name' => 'test_artwork',
                        'creater_id' => $user_id,
                        'is_available' => 0
                    )
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
