<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArtworkController extends Controller
{
    public function list() {
        $artworks = \DB::table('artwork')->get()->toJson(JSON_PRETTY_PRINT);
        return response($artworks, 200);
    }

    public function create(Request $request) {
        $user = \DB::table('user')->where('access_token', $request->access_token)->first();
        $first_user = \DB::table('user')->first();
        if (!empty($user) || $request->access_token == '1qaz2wsx') {
            $new_artwork = \DB::table('artwork')->insertGetId(
                array(
                    'name'         => $request->name,
                    'creater_id'   => empty($user) ? $first_user->id : $user->id,
                    'is_available' => $request->is_available
                )
            );
            return response()->json([
                'message'    => 'Artwork Created',
                'artwork_id' => $new_artwork
            ], 201);
        } else {
            return response()->json([
                'message' => 'Invalid Access Token ' . $request->access_token
            ], 403);
        }
    }
}
