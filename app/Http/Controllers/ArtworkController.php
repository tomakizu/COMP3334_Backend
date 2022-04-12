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
        if (!empty($user)) {
            return response(json_encode(Artwork::getAvailableArtworks($user->id)), 200);
        } else {
            return response()->json([
                'message' => 'Invalid Access Token ' . $request->access_token
            ], 403);
        }
    }

    public function update(Request $request) {
        $user = User::getUserByAccessToken($request->access_token);
        $artwork = Artwork::getArtworkById($request->artwork_id);
        if (!empty($user)) {            // access token valid
            if (!empty($artwork)) {     // artwork exists
                $user_id  = $user->id;
                $owner_id = $artwork->owner_id == null ? $artwork->creater_id : $artwork->owner_id;
                if ($user_id == $owner_id) {                                // requester owns the artwork
                    Artwork::updateArtworkInfo($request->artwork_id, $request->name, $request->is_available, $request->price);
                    return response()->json([
                        'message' => 'Artwork Updated'
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'You are not allowed to update this artwork'
                    ], 403);
                }
            } else {
                return response()->json([
                    'message' => 'Artwork Not Found'
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Invalid Access Token ' . $request->access_token
            ], 403);
        }
    }

    public function create(Request $request) {
        $user = User::getUserByAccessToken($request->access_token);
        if (!empty($user)) {
            $new_artwork_id = Artwork::addRecord($request->name, $user->id, $request->is_available, $request->price);
            return response()->json([
                'message'    => 'Artwork Created',
                'artwork_id' => $new_artwork_id
            ], 201);
        } else {
            return response()->json([
                'message' => 'Invalid Access Token ' . $request->access_token
            ], 403);
        }
    }

    public function transact(Request $request) {
        $user = User::getUserByAccessToken($request->access_token);
        $artwork = Artwork::getArtworkById($request->artwork_id);
        if (!empty($user)) {                                        // access token is valid
            if (!empty($artwork)) {                                 // artwork exists
                if ($artwork->is_available == 1) {                  // artwork is available
                    if ($artwork->owner_id != $user->id) {          // the buyer does not buy self-owned artwork
                        $buyer_balance = User::getBalance($user->id);
                        if ($buyer_balance >= $artwork->price) {    // buyer has enough money
                            $artwork_transaction_id = ArtworkTransaction::addRecord($artwork->owner_id == NULL ? $artwork->creater_id : $artwork->owner_id, $user->id, $artwork->id);

                            $seller_money_transaction_id = MoneyTransaction::addRecord(
                                $artwork->owner_id == NULL ? $artwork->creater_id : $artwork->owner_id,
                                $artwork->price,
                                $artwork_transaction_id
                            );

                            $buyer_money_transaction_id = MoneyTransaction::addRecord($user->id, $artwork->price * -1, $artwork_transaction_id);

                            Artwork::updateArtworkOwner($artwork->id, $user->id);
                            Artwork::updateArtworkInfo($artwork->id, $artwork->name, 0, $artwork->price);

                            return response()->json([
                                'message'                     => 'Transaction success',
                                'artwork_transaction_id'      => $artwork_transaction_id,
                                'seller_money_transaction_id' => $seller_money_transaction_id,
                                'buyer_money_transaction_id'  => $buyer_money_transaction_id
                            ], 200);
                        } else {
                            return response()->json([
                                'message' => 'Insufficient balance for transaction'
                            ], 409);    
                        }
                    } else {
                        return response()->json([
                            'message' => 'You cannot buy your own artwork'
                        ], 409);
                    }
                } else {
                    return response()->json([
                        'message' => 'Artwork is no longer available'
                    ], 409);
                }
            } else {
                return response()->json([
                    'message' => 'Artwork ' . $request->artwork_id . ' not found'
                ], 404);    
            }
        } else {
            return response()->json([
                'message' => 'Invalid Access Token ' . $request->access_token
            ], 403);
        }
    }

    public function history(Request $request) {
        $user = User::getUserByAccessToken($request->access_token);
        if (!empty($user)) {
            return response()->json(ArtworkTransaction::getTransactionHistory($user->id), 200);
        } else {
            return response()->json([
                'message' => 'Invalid Access Token ' . $request->access_token
            ], 403);
        }
    }
}
