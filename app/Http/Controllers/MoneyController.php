<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MoneyTransaction;
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
                $money_transaction_id = MoneyTransaction::addRecord($user->id, $request->value);
                return response()->json([
                    'message'        => 'Transaction success',
                    'transaction_id' => $money_transaction_id
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
            return response(json_encode(MoneyTransaction::getHistory($user->id)), 200);
        } else {
            return response()->json([
                'message' => 'Invalid Access Token ' . $request->access_token
            ], 403);
        }
    }
}
