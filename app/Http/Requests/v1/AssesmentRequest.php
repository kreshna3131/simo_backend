<?php

namespace App\Http\Requests\v1;

use App\Traits\AssesmentRules;
use Illuminate\Foundation\Http\FormRequest;

class AssesmentRequest extends FormRequest
{
    use AssesmentRules;
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
        return $this->requiredIf($this->subAssesment->template->id);
    }
}
