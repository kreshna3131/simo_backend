<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class ActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->blocked) {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message'   => trans('auth.banned')
            ], 401);
        }

        return $next($request);
    }
}
