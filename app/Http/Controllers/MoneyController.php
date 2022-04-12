<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class MoneyController extends Controller
{
    public function transact(Request $request) {
        $user = \DB::table('user')->where('access_token', $request->access_token)->first();
        if (!empty($user)) {
            if ($request->value < 0 && User::getBalance($user->id) + $request->value < 0) {
                return response()->json([
                    'message' => 'Insufficient balance for transaction'
                ], 409);    
            } else {
                $money_transaction = \DB::table('money_transaction')->insertGetId(
                    array(
                        'user_id' => $user->id,
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

    public function history(Request $request) {
        $user = \DB::table('user')->where('access_token', $request->access_token)->first();
        if (!empty($user)) {
            $transactions = \DB::table('money_transaction')->where('user_id', $user->id)->get()->toJson(JSON_PRETTY_PRINT);
            return response($transactions, 200);
        } else {
            return response()->json([
                'message' => 'Invalid Access Token ' . $request->access_token
            ], 403);
        }
    }
}
