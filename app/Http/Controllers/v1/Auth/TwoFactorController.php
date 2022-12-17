<?php

namespace App\Http\Controllers\v1\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\TwoFactorNotification;
use Exception;

class TwoFactorController extends Controller
{    
    /**
     * Menyimpan dan menyamakan two factor 
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        $request->validate([
            'two_factor_code' => ['required', 'integer'],
        ]);

        try {
            /** @var \App\Models\User */
            $user = auth()->user();

            if (!$user) {
                throw new Exception('You must login first', 401);
            }
    
            if (
                $request->input('two_factor_code') != optional($user->twoFactors->where('two_factor_ip', $request->ip())->first())->two_factor_code
            ) {
                throw new Exception('The two factor code you have entered does not match', 401);
            }

            $user->resetTwoFactorCode($request->ip());
    
            return response()->json([
                'message' => 'Two factor code verified successfully'
            ]);
        } catch (\Throwable $th) {
            if ($th->getCode() === 401) {
                return response()->json([
                    'message' => $th->getMessage()
                ], 401);
            }

            return response()->json([
                'message' => "Something went wrong"
            ], 500);
        }
    }
    
    /**
     * Mengirim kode two factor ulang
     *
     * @param  mixed $request
     * @return void
     */
    public function resend(Request $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();
        $code = $user->generateTwoFactorCode($request->ip());
        $user->notify(new TwoFactorNotification($code));

        return response()->json(['message' => 'The two factor code has been sent again']);
    }
}
