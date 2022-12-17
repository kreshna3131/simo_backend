<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
            'name' => ['required', 'unique:roles,name,'], 
            'note' => ['nullable', 'max:500'], 
            'permission' => ['required', 'array']
        ] :  [
            'name' => ['required', 'unique:roles,name,' . $this->role->id], 
            'note' => ['nullable', 'max:500'], 
            'permission' => ['required', 'array']
        ] ;
    }

    public function attributes()
    {
        return [
            'name' => 'Nama',
            'note' => 'Deskripsi Singkat'
        ];
    }
}
