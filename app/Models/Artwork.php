<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artwork extends Model
{
    use HasFactory;

    public static function getOwnedArtworks($user_id) {
        $artworks = \DB::table('artwork')->where('owner_id', $user_id)->get();

        foreach ($artworks as $artwork) {
            $artwork->owner_username   = User::getUsername($artwork->owner_id);
            $artwork->creater_username = User::getUsername($artwork->creater_id);
        }

        return $artworks;
    }

    public static function getCreatedArtworks($user_id) {
        $artworks = \DB::table('artwork')->where('creater_id', $user_id)->get();

        foreach ($artworks as $artwork) {
            $artwork->owner_username   = User::getUsername($artwork->owner_id);
            $artwork->creater_username = User::getUsername($artwork->creater_id);
        }

        return $artworks;

    }
}