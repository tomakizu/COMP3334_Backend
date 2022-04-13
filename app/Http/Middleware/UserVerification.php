<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class UserVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private $debug_token = '1qaz2wsx';
    
    public function handle(Request $request, Closure $next)
    {
        $user = User::getUserByAccessToken($request->access_token);
        if ($request->access_token == $this->debug_token) { // debug mode
            $request->access_token = User::handleDebugRequest($this->debug_token);
        } else if (empty($user)) {
            return response()->json([
                'message' => 'Invalid Access Token ' . $request->access_token
            ], 403);
        }
        return $next($request);
    }
}
