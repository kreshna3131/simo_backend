<?php

namespace App\Http\Middleware;

use App\Models\BannedIp;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Http\Request;

class IpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $banned_ip = BannedIp::where('ip_address', $request->ip());

        if ($banned_ip->exists()) {
            $banned_time = Carbon::parse($banned_ip->first()->created_at)->diffInMinutes(now());

            if ($banned_time > config('auth.ip_banned_duration')) {
                $banned_ip->first()->delete();

                return $next($request);
            }

            return response()->json([
                'message' => 'Your Ip has been banned.'
            ], 401);
        }

        return $next($request);
    }
}
