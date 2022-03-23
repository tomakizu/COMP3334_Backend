<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function list() {
        $artworks = \DB::table('user')->select('username', 'register_datetime')->get()->toJson(JSON_PRETTY_PRINT);
        return response($artworks, 200);
    }
}
