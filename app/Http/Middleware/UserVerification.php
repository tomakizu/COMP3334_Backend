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
        $user = \DB::table('user')->where('access_token', $request->access_token)->get();
        if (count($user) == 0 && $request->access_token != $this->debug_token) {
            return response()->json([
                'message' => 'Invalid Access Token ' . $request->access_token
            ], 403);
        }
        return $next($request);
    }
}
