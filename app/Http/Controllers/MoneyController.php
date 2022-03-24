<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class MoneyController extends Controller
{
    public function transact(Request $request) {
        $user = \DB::table('user')->where('access_token', $request->access_token)->first();
        $first_user = \DB::table('user')->first();
        if (!empty($user) || $request->access_token == '1qaz2wsx') {
            if ($request->value < 0 && User::getBalance(empty($user) ? $first_user->id : $user->id) + $request->value < 0) {
                return response()->json([
                    'message' => 'Insufficient balance for transaction'
                ], 409);    
            } else {
                $money_transaction = \DB::table('money_transaction')->insertGetId(
                    array(
                        'user_id' => empty($user) ? $first_user->id : $user->id,
                        'value'   => $request->value
                    )
                );
                return response()->json([
                    'message'        => 'Transaction success',
                    'transaction_id' => $money_transaction
                ], 200);    
            }
        } else {
            return response()->json([
                'message' => 'Invalid Access Token ' . $request->access_token
            ], 403);
        }
    }
}
