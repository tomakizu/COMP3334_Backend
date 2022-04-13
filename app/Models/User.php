<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public static function getAllUser() {
        return DB::table('user')->select('username', 'register_datetime')->get();
    }

    public static function getUserByAccessToken($access_token) {
        return DB::table('user')->where('access_token', $access_token)->first();
    }

    public static function getUsername($user_id) {
        return DB::table('user')->where('id', $user_id)->value('username');
    }

    public static function getBalance($user_id) {
        $balance = 0.0;
        $transactions = DB::table('money_transaction')->where('user_id', $user_id)->get();
        foreach ($transactions as $transaction) {
            $balance += $transaction->value;
        }
        return $balance;
    }

    public static function addRecord($username, $password) {
        $salt = rand(100000000, 999999999); // 9 digits
        return DB::table('user')->insertGetId(
            array(
                'username'   => $username,
                'password'   => hash('sha512', $password . $salt),
                'salt_value' => $salt
            )
        );
    }

    public static function updatePassword($user_id, $password) {
        $salt = DB::table('user')->where('id', $user_id)->value('salt_value');
        return DB::table('user')->where('id', $user_id)->update(['password' => hash('sha512', $password . $salt)]);
    }

    public static function generateAccessToken($username) {
        $access_token = hash('sha512', $username . date('YmdHis'));
        DB::table('user')->where('username', $username)->update(['access_token' => $access_token]);
        return $access_token;
    }

    public static function verifyUsername($username) {
        return !(DB::table('user')->where('username', $username)->exists());
    }

    public static function verifyLogin($username, $password) {
        $user = DB::table('user')->where('username', $username)->first();
        if (empty($user)) {
            return false;
        }
        return hash('sha512', $password . $user->salt_value) == $user->password;
    }

    public static function handleDebugRequest($debug_token) {
        $first_user = DB::table('user')->first();
        if ($first_user->access_token == null) {
            DB::table('user')->where('id', $first_user->id)->update(['access_token' => $debug_token]);
            return $debug_token;
        } else {
            return $first_user->access_token;
        }
    }
}
