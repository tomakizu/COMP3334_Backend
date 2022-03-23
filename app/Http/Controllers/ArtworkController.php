<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArtworkController extends Controller
{
    public function list() {
        $artworks = \DB::table('artwork')->get()->toJson(JSON_PRETTY_PRINT);
        return response($artworks, 200);
    }
}
