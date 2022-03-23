<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClientValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private $credential = 'fuckKonami';
    
    public function handle(Request $request, Closure $next)
    {
        if ($request->credential != $this->credential) {
            return response()->json([
                'message' => 'Invalid Credential'
            ], 403);
        }
        return $next($request);
    }
}
