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
    private $credential = '0daeed08bfd9c77436a5f94eaac641935f24a17345ce35941aca68e8644ffef59586701130db8a10a4cb408be9b929ed6f7021052186d0adc0ad90ba0401a442';
    
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
