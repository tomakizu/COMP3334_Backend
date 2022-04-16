<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artwork;
use App\Models\ArtworkTransaction;
use App\Models\MoneyTransaction;
use App\Models\User;

class UserController extends Controller
{
    public function list() {
        return response(json_encode(User::getAllUser()), 200);
    }

    public function details(Request $request) {
        $user = User::getUserByAccessToken($request->access_token);

        // return error if user is empty
        if (empty($user)) {
            return response()->json(['message' => 'Invalid Access Token ' . $request->access_token], 403);
        }

        return response()->json([
            'user_id'                     => $user->id,
            'username'                    => $user->username,
            'balance'                     => User::getBalance($user->id),
            'created_artwork'             => Artwork::getCreatedArtworks($user->id),
            'owned_artwork'               => Artwork::getOwnedArtworks($user->id),
            'artwork_transaction_history' => ArtworkTransaction::getTransactionHistory($user->id),
            'money_transaction'           => MoneyTransaction::getTransactionHistory($user->id)
        ], 200);
    }

    public function update(Request $request) {
        $user = User::getUserByAccessToken($request->access_token);

        // return error if user is empty
        if (empty($user)) {
            return response()->json(['message' => 'Invalid Access Token ' . $request->access_token], 403);
        }

        User::updatePassword($user->id, $request->password);
        return response()->json(['message' => 'Password Updated'], 200);
    }

    public function create(Request $request) {
        if (User::verifyUsername($request->username)) {
            $new_user_id = User::addRecord($request->username, $request->password);
            return response()->json(['message' => 'User Created', 'user_id' => $new_user_id], 201);
        }
        return response()->json(['message' => 'Username ' . $request->username . ' already exists'], 406);
    }

    public function login(Request $request) {
        if (User::verifyLogin($request->username, $request->password)) {
            $access_token = User::generateAccessToken($request->username);
            return response()->json(['message' => 'Success','access_token' => $access_token], 200);    
        }
        return response()->json(['message' => 'invalid user credentials'], 404);
    }

    public function balance(Request $request) {
        $user = User::getUserByAccessToken($request->access_token);
        
        // return error if user is empty
        if (empty($user)) {
            return response()->json(['message' => 'user not found'], 404);
        }

        $balance = User::getBalance($user->id);
        return response()->json(['user_id' => $user->id, 'balance' => $balance], 200);
    }
}
