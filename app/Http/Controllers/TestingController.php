<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artwork;
use App\Models\ArtworkTransaction;
use App\Models\MoneyTransaction;
use App\Models\User;

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

    public function truncateDatabase() {
        MoneyTransaction::truncate();
        ArtworkTransaction::truncate();
        Artwork::truncate();
        User::truncate();
    }
}
