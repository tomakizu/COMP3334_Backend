<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ArtworkTransaction extends Model
{
    use HasFactory;

    public static function getTransactionHistory($user_id) {
        $histories = DB::table('artwork_transaction')->where('seller_id', $user_id)->orWhere('buyer_id', $user_id)->get();
        foreach ($histories as $history) {
            $history->buyer_username  = User::getUsername($history->buyer_id);
            $history->seller_username = User::getUsername($history->seller_id);
        }
        return $histories;
    }

    public static function addRecord($seller_id, $buyer_id, $artwork_id) {
        return DB::table('artwork_transaction')->insertGetId(
            array(
                'seller_id' => $seller_id,
                'buyer_id'  => $buyer_id,
                'artwork_id' => $artwork_id
            )
        );
    }
}
