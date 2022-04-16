<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HashPasswordWithSalt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('user')) {
            if (!Schema::hasColumn('user', 'salt_value')) {
                Schema::table('user', function (Blueprint $table) {
                    $table->integer('salt_value')->default(0);
                });
            }

            $users = \DB::table('user')->get();
            foreach ($users as $user) {
                $salt = rand(100000000, 999999999); // 9 digits
                \DB::table('user')->where('id', $user->id)->update(['salt_value' => $salt]);
                $hashed_password = hash('sha512', $user->password);
                $new_password = hash('sha512', $hashed_password . $salt);
                \DB::table('user')->where('id', $user->id)->update(['password' => $new_password]);
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
        if (Schema::hasTable('user')) {
            Schema::table('user', function (Blueprint $table) {
                $table->dropColumn('salt_value');
            });
            \DB::table('user')->update(['password' => '123456']);
        }
    }
}
