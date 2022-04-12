<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyTransaction extends Model
{
    use HasFactory;

    public static function getHistory($user_id) {
        return \DB::table('money_transaction')->where('user_id', $user_id)->get();
    }
}
