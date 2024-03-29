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

    public static function addRecord($artwork_name, $creater_id, $is_available, $price, $filename) {
        return DB::table('artwork')->insertGetId(
            array(
                'name'         => $artwork_name,
                'creater_id'   => $creater_id,
                'owner_id'     => $creater_id,
                'is_available' => $is_available,
                'price'        => $price,
                'filename'     => $filename
            )
        );
    }

    public static function updateArtworkInfo($artwork_id, $artwork_name, $is_available, $price, $filename = null) {
        $artwork = static::getArtworkById($artwork_id);

        $update_array = array();

        // return error if artwork is empty
        if (empty($artwork)) {
            return response()->json(['message' => 'Artwork ' . $artwork_id . ' not found'], 404);
        }

        if (!is_null($artwork_name)) {
            $update_array['name'] = $artwork_name;
        }

        if (!is_null($is_available)) {
            $update_array['is_available'] = $is_available;
        }

        if (!is_null($price)) {
            $update_array['price'] = $price;
        }

        if (!is_null($filename)) {
            $update_array['filename'] = $filename;
        }
        
        return DB::table('artwork')->where('id', $artwork_id)->update($update_array);
    }

    public static function updateArtworkOwner($artwork_id, $owner_id) {
        return DB::table('artwork')->where('id', $artwork_id)->update(['owner_id' => $owner_id]);
    }

    public static function truncate() {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('artwork')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}