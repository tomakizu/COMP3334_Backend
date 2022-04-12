<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MoneyTransaction extends Model
{
    use HasFactory;

    public static function getTransactionHistory($user_id) {
        return DB::table('money_transaction')->where('user_id', $user_id)->get();
    }

    public static function addRecord($user_id, $value, $artwork_transaction_id = null) {
        return DB::table('money_transaction')->insertGetId(
            array(
                'user_id'                => $user_id,
                'value'                  => $value,
                'artwork_transaction_id' => $artwork_transaction_id
            )
        );
    }
}
