<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artwork;
use App\Models\ArtworkTransaction;
use App\Models\MoneyTransaction;
use App\Models\User;

class ArtworkController extends Controller
{
    public function list(Request $request) {
        $user = User::getUserByAccessToken($request->access_token);

        // return error if user is empty
        if (empty($user)) {
            return response()->json(['message' => 'Invalid Access Token ' . $request->access_token], 403);
        }

        return response(json_encode(Artwork::getAvailableArtworks($user->id)), 200);
    }

    public function update(Request $request) {
        $user = User::getUserByAccessToken($request->access_token);
        $artwork = Artwork::getArtworkById($request->artwork_id);

        // return error if user is empty
        if (empty($user)) {
            return response()->json(['message' => 'Invalid Access Token ' . $request->access_token], 403);
        }

        // return error if artwork is empty
        if (empty($artwork)) {
            return response()->json(['message' => 'Artwork ' . $request->artwork_id . ' not found'], 404);
        }

        // return error if artwork is not owned by user
        $owner_id = $artwork->owner_id == null ? $artwork->creater_id : $artwork->owner_id;
        if ($owner_id != $user->id) {
            return response()->json(['message' => 'You are not allowed to update this artwork'], 403);
        }

        Artwork::updateArtworkInfo($request->artwork_id, $request->name, $request->is_available, $request->price);
        return response()->json(['message' => 'Artwork Updated'], 200);
    }

    public function create(Request $request) {
        $user = User::getUserByAccessToken($request->access_token);

        // return error if user is empty
        if (empty($user)) {
            return response()->json(['message' => 'Invalid Access Token ' . $request->access_token], 403);
        }

        // return error if price is negative
        if ($request->price < 0) {
            return response()->json(['message' => 'Price cannot be negative'], 400);
        }

        $new_artwork_id = Artwork::addRecord($request->name, $user->id, $request->is_available, $request->price);

        return response()->json(['message' => 'Artwork Created','artwork_id' => $new_artwork_id], 201);
    }

    public function transact(Request $request) {
        $user = User::getUserByAccessToken($request->access_token);
        $artwork = Artwork::getArtworkById($request->artwork_id);

        // return error if user is empty
        if (empty($user)) {
            return response()->json(['message' => 'Invalid Access Token ' . $request->access_token], 403);
        }

        // return error if artwork is empty
        if (empty($artwork)) {
            return response()->json(['message' => 'Artwork ' . $request->artwork_id . ' not found'], 404);
        }

        // return error if artwork is not available
        if ($artwork->is_available == 0) {
            return response()->json(['message' => 'Artwork ' . $request->artwork_id . ' is not available'], 409);
        }

        // return error if the buyer is the artwork owner
        if ($user->id == $artwork->owner_id) {
            return response()->json(['message' => 'You cannot buy your own artwork'], 409);
        }

        // return error if the buyer does not have enough balance
        $buyer_balance = User::getBalance($user->id);
        if ($buyer_balance < $request->value) {
            return response()->json(['message' => 'Insufficient balance for transaction'], 409);
        }

        $artwork_transaction_id = ArtworkTransaction::addRecord(
            $artwork->owner_id == NULL ? $artwork->creater_id : $artwork->owner_id, 
            $user->id, 
            $artwork->id
        );

        $seller_money_transaction_id = MoneyTransaction::addRecord(
            $artwork->owner_id == NULL ? $artwork->creater_id : $artwork->owner_id,
            $artwork->price,
            $artwork_transaction_id
        );

        $buyer_money_transaction_id = MoneyTransaction::addRecord($user->id, $artwork->price * -1, $artwork_transaction_id);

        Artwork::updateArtworkOwner($artwork->id, $user->id);
        $row = Artwork::updateArtworkInfo($artwork->id, null, 0, null);

        return response()->json([
            'message'                     => 'Transaction success',
            'artwork_transaction_id'      => $artwork_transaction_id,
            'seller_money_transaction_id' => $seller_money_transaction_id,
            'buyer_money_transaction_id'  => $buyer_money_transaction_id
        ], 200);
    }

    public function history(Request $request) {
        $user = User::getUserByAccessToken($request->access_token);

        // return error if user is empty
        if (empty($user)) {
            return response()->json(['message' => 'Invalid Access Token ' . $request->access_token], 403);
        }

        return response()->json([
            'username' => $user->username,
            'history'  => ArtworkTransaction::getTransactionHistory($user->id)
        ], 200);
    }
}
