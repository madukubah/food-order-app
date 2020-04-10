<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roleCode)
    {
        if( $request->user()->role == $roleCode )
            return $next($request);
        else
            return response()->json([
                'status' => 'error',
                'errors' => "no in role"
            ], 403);
    }
}