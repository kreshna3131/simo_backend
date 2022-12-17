<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use Illuminate\Http\Request;

class TwoFactor
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
        /** @var \App\Models\User */
        $user = $request->user();
        $two_factor = 0;

        if (
            $user
            && $user->twoFactors->contains('two_factor_ip', $request->ip())
            && optional($user->twoFactors->where('two_factor_ip', $request->ip())->first())->two_factor_code
            && $two_factor == 1
        ) {
            $two_factor = $user->twoFactors->where('two_factor_ip', $request->ip())->first();

            if ($two_factor->two_factor_expires_at->lt(now())) {
                $user->deleteTwoFactorCode($request->ip());

                $user->currentAccessToken()->delete();

                return response()->json([
                    'message' => 'The two factor code has expired. Please login again.'
                ], 401);
            }

            return response()->json([
                'message' => 'Please verify the two factor code'
            ], 401);
        }

        return $next($request);
    }
}
