<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MoneyTransaction;
use App\Models\User;

class MoneyController extends Controller
{
    public function transact(Request $request) {
        $user = User::getUserByAccessToken($request->access_token);

        // return error if user is empty
        if (empty($user)) {
            return response()->json(['message' => 'Invalid Access Token ' . $request->access_token], 403);
        }

        // return error if user has not enough money
        if ($request->value < 0 && User::getBalance($user->id) + $request->value < 0) {
            return response()->json(['message' => 'Insufficient balance for transaction'], 409);    
        }

        $money_transaction_id = MoneyTransaction::addRecord($user->id, $request->value);
        return response()->json(['message' => 'Transaction success', 'transaction_id' => $money_transaction_id], 200);
    }

    public function history(Request $request) {
        $user = User::getUserByAccessToken($request->access_token);

        // return error if user has not enough money
        if (empty($user)) {
            return response()->json(['message' => 'Invalid Access Token ' . $request->access_token], 403);
        }

        return response(json_encode(MoneyTransaction::getTransactionHistory($user->id)), 200);
    }
}
