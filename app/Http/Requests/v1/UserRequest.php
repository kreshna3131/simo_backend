<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return $this->method() === "POST"
        ? [
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'sip_number' => ['nullable', 'string', 'unique:users,sip_number'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'numeric'],
            'kode_provider' => ['nullable', 'string'],
            'specialist_id' => ['nullable', 'numeric'],
            'blocked' => ['required', 'numeric']
        ] : [
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'sip_number' => ['nullable', 'string', 'unique:users,sip_number,'. $this->user->id],
            'email' => ['required', 'email', 'unique:users,email,'. $this->user->id],
            'role' => ['required', 'numeric'],
            'kode_provider' => ['nullable', 'string'],
            'specialist_id' => ['nullable', 'numeric'],
            'blocked' => ['required', 'numeric']
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nama',
            'gender' => 'Jenis kelamin',
            'email' => 'Email',
            'role' => 'Role',
            'specialist_id' => 'Spesialis',
            'blocked' => 'Status'
        ];
    }
}
