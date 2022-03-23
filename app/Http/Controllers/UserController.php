<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function list() {
        $artworks = \DB::table('user')->select('username', 'register_datetime')->get()->toJson(JSON_PRETTY_PRINT);
        return response($artworks, 200);
    }

    public function create(Request $request) {
        $user = \DB::table('user')->where('username', $request->username)->get();
        if (count($user) == 0) {
            $new_user = \DB::table('user')->insertGetId(
                array(
                    'username' => $request->username,
                    'password' => $request->password
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
        $user = \DB::table('user')->where('username', $request->username)->where('password', $request->password)->first();
        if (!empty($user)) {
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
    }
}
