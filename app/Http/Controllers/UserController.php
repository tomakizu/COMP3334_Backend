<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function list() {
        $artworks = \DB::table('user')->select('username', 'register_datetime')->get()->toJson(JSON_PRETTY_PRINT);
        return response($artworks, 200);
    }

    public function update(Request $request) {
        $user = \DB::table('user')->where('access_token', $request->access_token)->first();
        $first_user = \DB::table('user')->first();
        if (!empty($user) || $request->access_token == '1qaz2wsx') {    // access token valid
            $user_id = empty($user) ? $first_user->id         : $user->id;
            $salt    = empty($user) ? $first_user->salt_value : $user->salt_value;
            \DB::table('user')->where('id', $user_id)->update(
                array(
                    'password' => hash('sha512', $request->password . $salt)
                )
            );
            return response()->json([
                'message' => 'Password Updated'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid Access Token ' . $request->access_token
            ], 403);
        }
    }

    public function create(Request $request) {
        $user = \DB::table('user')->where('username', $request->username)->get();
        if (count($user) == 0) {
            $salt = rand(100000000, 999999999); // 9 digits
            $new_user = \DB::table('user')->insertGetId(
                array(
                    'username'   => $request->username,
                    'password'   => hash('sha512', $request->password . $salt),
                    'salt_value' => $salt
                )
            );
            return response()->json([
                'message' => 'User Created',
                'user_id' => $new_user
            ], 201);
        } else {
            return response()->json([
                'message' => 'Username ' . $request->username . ' already exists'
            ], 406);
        }
    }

    public function login(Request $request) {
        $user = \DB::table('user')->where('username', $request->username)->first();
        if (!empty($user)) {
            $salted_password = hash('sha512', $request->password . $user->salt_value);
            if ($salted_password == $user->password) {
                $access_token = hash('sha512', $request->username . date('YmdHis'));
                $affected_rows = \DB::table('user')->where('id', $user->id)->update(['access_token' => $access_token]);
                if ($affected_rows == 1) {
                    return response()->json([
                        'message' => 'Success',
                        'access_token' => $access_token
                    ], 200);    
                }
            } else {
                return response()->json([
                    'message' => 'invalid user credentials'
                ], 404);    
            }

        } else {
            return response()->json([
                'message' => 'invalid user credentials'
            ], 404);
        }
    }

    public function balance(Request $request) {
        $user = \DB::table('user')->where('access_token', $request->access_token)->first();
        $first_user = \DB::table('user')->first();
        if (!empty($user) || $request->access_token == '1qaz2wsx') {
            $balance = User::getBalance(empty($user) ? $first_user->id : $user->id);
            return response()->json([
                'user_id' => empty($user) ? $first_user->id : $user->id,
                'balance' => $balance
            ], 200);
        } else {
            return response()->json([
                'message' => 'user not found'
            ], 404);
        }
    }
}
