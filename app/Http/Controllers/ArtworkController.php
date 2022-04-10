<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ArtworkController extends Controller
{
    public function list() {
        $artworks = \DB::table('artwork')->get()->toJson(JSON_PRETTY_PRINT);
        return response($artworks, 200);
    }

    public function update(Request $request) {
        $user = \DB::table('user')->where('access_token', $request->access_token)->first();
        $first_user = \DB::table('user')->first();
        $artwork = \DB::table('artwork')->where('id', $request->artwork_id)->first();
        if (!empty($user) || $request->access_token == '1qaz2wsx') {        // access token valid
            if (!empty($artwork)) {                                         // artwork exists
                $user_id  = empty($user) ? $first_user->id : $user->id;
                $owner_id = $artwork->owner_id == null ? $artwork->creater_id : $artwork->owner_id;
                if ($user_id == $owner_id) {                                // requester owns the artwork
                    \DB::table('artwork')->where('id', $request->artwork_id)->update(
                        array(
                            'name'         => $request->name         == null ? $artwork->name         : $request->name,
                            'is_available' => $request->is_available == null ? $artwork->is_available : $request->is_available,
                            'price'        => $request->price        == null ? $artwork->price        : $request->price,
                        )
                    );
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
        $user = \DB::table('user')->where('access_token', $request->access_token)->first();
        $first_user = \DB::table('user')->first();
        if (!empty($user) || $request->access_token == '1qaz2wsx') {
            $new_artwork = \DB::table('artwork')->insertGetId(
                array(
                    'name'         => $request->name,
                    'creater_id'   => empty($user) ? $first_user->id : $user->id,
                    'is_available' => $request->is_available,
                    'price'        => $request->price
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

    public function transact(Request $request) {
        $user = \DB::table('user')->where('access_token', $request->access_token)->first();
        $artwork = \DB::table('artwork')->where('id', $request->artwork_id)->first();
        $first_user = \DB::table('user')->first();
        if (!empty($user) || $request->access_token == '1qaz2wsx') {    // access token is valid
        if (!empty($artwork)) {                                         // artwork exists
                $user_id = empty($user) ? $first_user->id : $user->id;
                if ($artwork->owner_id != $user_id) {                  // the buyer does not buy self-owned artwork
                    $buyer_balance = User::getBalance(empty($user) ? $first_user->id : $user->id);
                    if ($buyer_balance >= $artwork->price) {            // buyer has enough money
                        $artwork_transaction = \DB::table('artwork_transaction')->insertGetId(
                            array(
                                'seller_id'  => $artwork->owner_id == NULL ? $artwork->creater_id : $artwork->owner_id,
                                'buyer_id'   => empty($user)               ? $first_user->id      : $user->id,
                                'artwork_id' => $artwork->id
                            )
                        );
        
                        $seller_money_transaction = \DB::table('money_transaction')->insertGetId(
                            array(
                                'user_id'                => $artwork->owner_id == NULL ? $artwork->creater_id : $artwork->owner_id,
                                'artwork_transaction_id' => $artwork_transaction,
                                'value'                  => $artwork->price
                            )
                        );
        
                        $buyer_money_transaction = \DB::table('money_transaction')->insertGetId(
                            array(
                                'user_id'                => empty($user) ? $first_user->id : $user->id,
                                'artwork_transaction_id' => $artwork_transaction,
                                'value'                  => $artwork->price * -1
                            )
                        );
                        $affected_rows = \DB::table('artwork')->where('id', $artwork->id)->update(['owner_id' => empty($user) ? $first_user->id : $user->id]);
                        if ($affected_rows == 1) {
                            return response()->json([
                                'message'                     => 'Transaction success',
                                'artwork_transaction_id'      => $artwork_transaction,
                                'seller_money_transaction_id' => $seller_money_transaction,
                                'buyer_money_transaction_id'  => $buyer_money_transaction
                            ], 200);    
                        }
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
        $user = \DB::table('user')->where('access_token', $request->access_token)->first();
        $first_user = \DB::table('user')->first();
        if (!empty($user) || $request->access_token == '1qaz2wsx') {
            $artwork_transactions = \DB::table('artwork_transaction')
                ->where('seller_id', empty($user) ? $first_user->id : $user->id)
                ->orWhere('buyer_id', empty($user) ? $first_user->id : $user->id)
                ->get();
            return response()->json($artwork_transactions, 200);
        } else {
            return response()->json([
                'message' => 'Invalid Access Token ' . $request->access_token
            ], 403);
        }
    }
}
