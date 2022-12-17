<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IntegrationResultRequest extends FormRequest
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
            'integration' => ['required'],
            'keluhan' => ['required'],
            'keadaan_umum' => ['required'],
            'tindakan_resusitasi' => ['required'],
            'tekanan_darah' => ['required'],
            'frekuensi_nadi' => ['required'],
            'frekuensi_napas' => ['required'],
            'berat_badan' => ['required'],
            'tinggi_badan' => ['required'],
            'suhu_badan' => ['required'],
            'gds' => ['required'],
            'diagnosis_kerja' => ['required_if:integration, ==, medis'],
            'diagnosis_keperawatan' => ['required_if:integration, ==, keperawatan'],
            'rencana_terapi' => ['required_if:integration, ==, medis'],
            'rencana_tindak_lanjut' => ['required_if:integration, ==, medis'],
            'implementasi' => ['required_if:integration, ==, keperawatan']
        ];
    }
}
