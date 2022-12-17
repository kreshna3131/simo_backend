<?php

namespace App\Http\Controllers\v1\Auth;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\v1\Auth\ResetPasswordRequest;
use App\Http\Requests\v1\Auth\ForgotPasswordRequest;
use App\Models\BannedIp;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\RateLimiter;

class ForgotPasswordController extends Controller
{    
    /**
     * Mengirim reset password
     *
     * @param  mixed $request
     * @return void
     */
    public function sendResetLink(ForgotPasswordRequest $request)
    {
        $this->checkTooManyFailedAttempts($request);

        $hit = 0;
        $decaySeconds = 0;
        try {
            $user = User::where('email', $request->email)->first();

            if(!$user) {
                $hit = RateLimiter::hit($this->throttleKey(), $seconds = 60);

                if ($hit === 5) {
                    $hit = $hit;
                    $decaySeconds = $seconds;
                    throw new Exception('Throttle request', 401);
                }
            }

            $status = Password::sendResetLink(
                $request->validated()
            );

            return response()->json(['status' => __($status)]);
        } catch (\Throwable $th) {
            info($th);

            if ($hit === 5) {
                return response()->json([
                    'decay_second' => $decaySeconds,
                    'message' => $th->getMessage()
                ], $th->getCode());
            }

            return response()->json(['email' => 'Gagal harap coba lagi'], 500);
        }

        // return $status === Password::RESET_LINK_SENT
        //     ? response()->json(['status' => __($status)])
        //     : response()->json(['email' => __($status)], 500);
    }
    
    /**
     * Reset password
     *
     * @param  mixed $request
     * @return void
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $status = Password::reset(
                $request->validated(),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));
    
                    $user->save();
    
                    event(new PasswordReset($user));
                }
            );
    
            $user = User::where('email', $request->email)->first();
            
            return response()->json([
                'status' => __($status),
                'token' => $user->createToken("login")->plainTextToken
            ]);
        } catch (\Throwable $th) {
            info($th);
            response()->json(['email' => __($status)], 500);
        }
        

        // info($status);

        // return $status === Password::PASSWORD_RESET
        //     ? response()->json(['status' => __($status), 'token' => $user->createToken("login")->plainTextToken])
        //     : response()->json(['email' => __($status)], 500);
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkToken(Request $request)
    {
        try {
            if (!$request->token) {
                throw new Exception('null token', 401);
            } else {
                $checkToken = DB::table('password_resets')->where('email', $request->email)->first();
                // Jika token tidak ada
                if (!$checkToken) {
                    throw new Exception('null token', 401);
                }
                // Jika token tidak valid
                if (!Hash::check($request->token, $checkToken->token)) {
                    throw new Exception('invalid token', 401);
                }
                // Jika token expired
                if ($this->tokenExpired($checkToken->created_at)) {
                    throw new Exception('expired token', 401);
                };  
            }

            return response()->json([
                'message' => 'valid token'
            ]);
        } catch (\Throwable $th) {
            info($th);
            if ($th->getCode() === 401) {
                return response()->json([
                    'message' => $th->getMessage()
                ], $th->getCode());
            }

            return response()->json([
                'message' => "Something went wrong. Please try again later"
            ], 500);
        }
        
    }

    /**
     * Determine if the token has expired.
     *
     * @param  string  $createdAt
     * @return bool
     */
    protected function tokenExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addSeconds(config('auth.passwords.users.expire') * 60)->isPast();
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
