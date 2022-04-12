<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Artwork extends Model
{
    use HasFactory;

    public static function getAvailableArtworks($user_id) {
        $artworks = DB::table('artwork')->where('is_available', 1)->where(
            DB::raw('CASE WHEN owner_id IS NULL THEN creater_id ELSE owner_id END'), '<>', $user_id
        )->get();
        foreach ($artworks as $artwork) {
            $artwork->owner_username   = User::getUsername($artwork->owner_id);
            $artwork->creater_username = User::getUsername($artwork->creater_id);
        }
        return $artworks;
    }

    public static function getArtworkById($artwork_id) {
        return DB::table('artwork')->where('id', $artwork_id)->first();
    }

    public static function getOwnedArtworks($user_id) {
        $artworks = DB::table('artwork')->where('owner_id', $user_id)->get();

        foreach ($artworks as $artwork) {
            $artwork->owner_username   = User::getUsername($artwork->owner_id);
            $artwork->creater_username = User::getUsername($artwork->creater_id);
        }

        return $artworks;
    }

    public static function getCreatedArtworks($user_id) {
        $artworks = DB::table('artwork')->where('creater_id', $user_id)->get();

        foreach ($artworks as $artwork) {
            $artwork->owner_username   = User::getUsername($artwork->owner_id);
            $artwork->creater_username = User::getUsername($artwork->creater_id);
        }

        return $artworks;
    }

    public static function addRecord($artwork_name, $creater_id, $is_available, $price) {
        return DB::table('artwork')->insertGetId(
            array(
                'name'         => $artwork_name,
                'creater_id'   => $creater_id,
                'is_available' => $is_available,
                'price'        => $price
            )
        );
    }

    public static function updateArtworkInfo($artwork_id, $artwork_name, $is_available, $price) {
        $artwork = static::getArtworkById($artwork_id);
        return DB::table('artwork')->where('id', $artwork_id)->update(
            array(
                'name'         => $artwork_name == null ? $artwork->name         : $artwork_name,
                'is_available' => $is_available == null ? $artwork->is_available : $is_available,
                'price'        => $price        == null ? $artwork->price        : $price,
            )
        );

    }

    public static function updateArtworkOwner($artwork_id, $owner_id) {
        return DB::table('artwork')->where('id', $artwork_id)->update(['owner_id' => $owner_id]);
    }
}