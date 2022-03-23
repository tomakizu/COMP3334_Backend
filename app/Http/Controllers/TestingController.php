<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestingController extends Controller
{
    public function test200() {
        return response()->json([
            'message' => 'response: 200'
        ], 200);
    }

    public function test404() {
        return response()->json([
            'message' => 'response: 404'
        ], 404);
    }

    public function test500() {
        return response()->json([
            'message' => 'response: 500'
        ], 500);
    }
}
