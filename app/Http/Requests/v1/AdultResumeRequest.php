<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class AdultResumeRequest extends FormRequest
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
        return [
            'resume_date'     => ['required'],
            'resume_time'     => ['required'],
            'diagnosis'       => ['required', 'string', 'max:200'],
            'terapi'          => ['required', 'string', 'max:200'],
            'riwayat_tindakan'=> ['required', 'string', 'max:200'],
        ];
    }
}
