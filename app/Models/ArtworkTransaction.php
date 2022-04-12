<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtworkTransaction extends Model
{
    use HasFactory;

    public static function getHistory($user_id) {
        $histories = \DB::table('artwork_transaction')->where('seller_id', $user_id)->orWhere('buyer_id', $user_id)->get();
        foreach ($histories as $history) {
            $history->buyer_username  = User::getUsername($history->buyer_id);
            $history->seller_username = User::getUsername($history->seller_id);
        }
        return $histories;
    }
}
