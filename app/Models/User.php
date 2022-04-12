<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public static function getUsername($user_id) {
        return \DB::table('user')->where('id', $user_id)->value('username');
    }

    public static function getBalance($id) {
        $balance = 0.0;
        $transactions = \DB::table('money_transaction')->where('user_id', $id)->get();
        foreach ($transactions as $transaction) {
            $balance += $transaction->value;
        }
        return $balance;
    }
}
