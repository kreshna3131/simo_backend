<?php

namespace App\Http\Requests\v1\Auth;

use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest as AuthEmailVerificationRequest;

class EmailVerificationRequest extends AuthEmailVerificationRequest
{
    /**
     * Get the user making the request.
     *
     * @param  string|null  $guard
     * @return mixed
     */
    public function user($guard = null)
    {
        return User::find($this->route('id'));
    }
}
