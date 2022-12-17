<?php

namespace App\Http\Controllers\v1\Auth;

use Exception;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\v1\Auth\LoginRequest;
use App\Models\BannedIp;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{    
    /**
     * login
     *
     * @param  mixed $request
     * @return void
     */
    public function login(LoginRequest $request)
    {
        $this->checkTooManyFailedAttempts($request);

        /** @var User*/
        $user = User::where(['email' => $request->email])->first();

        $two_factor = 0;

        $validCredential = Auth::attempt($request->validated());

        $hit = 0;
        $decaySeconds = 0;

        try {
            if (!$user || !$validCredential) {
                $hit = RateLimiter::hit($this->throttleKey(), $seconds = 60);

                if ($hit === 5) {
                    $hit = $hit;
                    $decaySeconds = $seconds;
                    throw new Exception('Throttle request', 401);
                }
                
                throw new Exception('Invalid credentials', 401);
            }

            if ($user->blocked) {
                throw new Exception('You has been blocked', 401);
            }

            if (!in_array($request->ip(), $this->getLoggedIp($user)) && $two_factor == 1) {
                $code = $user->generateTwoFactorCode(
                    $request->ip()
                );

                $user->sendTwoFactorCodeNotification($code);

                return response()->json([
                    'token'   => $request->user()->createToken("login")->plainTextToken,
                    'message' => 'Please verify the two factor code'
                ]);
            }

            return response()->json([
                'token'   => $request->user()->createToken("login")->plainTextToken,
                'message' => 'Login success'
            ]);
        } catch (\Throwable $th) {
            if ($hit === 5) {
                return response()->json([
                    'decay_second' => $decaySeconds,
                    'message' => $th->getMessage()
                ], $th->getCode());
            }

            if ($th->getCode() === 401) {
                return response()->json([
                    'message' => $th->getMessage()
                ], $th->getCode());
            }
            info($th);

            return response()->json([
                'message' => "Something went wrong. Please try again later"
            ], 500);
        } 
    }

    /**
     * Mengambil ip yang ada di database
     *
     * @param  mixed $user
     * @return array
     */
    protected function getLoggedIp(User $user): array
    {
        return $user->twoFactors->pluck('two_factor_ip')->toArray();
    }
    
    /**
     * logout
     *
     * @param  mixed $request
     * @return void
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out success'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Logout failed'
            ], 500);
        }
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower(request('email')) . '|' . request()->ip();
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     */
    public function checkTooManyFailedAttempts(Request $request)
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $this->saveIpToDatabase($request);

        throw new Exception('IP address banned. Too many login attempts.', 500);
    }

    /**
     * Menyimpan ip yang dibanned ke database
     *
     * @param Request $request
     *
     * @return void
     */
    protected function saveIpToDatabase(Request $request)
    {
        $ip = BannedIp::where('ip_address', $request->ip());

        if (!$ip->exists()) {
            BannedIp::create([
                'ip_address' => $request->ip()
            ]);
        }
    }
}
