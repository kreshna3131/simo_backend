<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class MeasureRadRequest extends FormRequest
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
        switch ($this->method()) {
            case 'PATCH' :
                return [
                    'name' => 'required|string|min:1|max:255'
                ];
            
            default :
                return [
                    'name' => 'required|string|min:1|max:255',
                    'status' => 'string',
                ];
        }
    }
}