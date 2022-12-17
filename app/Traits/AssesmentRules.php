<?php

namespace App\Traits;

use App\Models\Attribute;

trait AssesmentRules
{    
    /**
     * listing attributes yang aktif
     */
    public function listingAttributes($templateId)
    {
        $attributes = Attribute::select(
            'attribute_template.id', 
            'attributes.type', 
            'attributes.label', 
            'attributes.name', 
            'attribute_template.rules', 
            'attribute_template.group_name', 
        )
        ->leftJoin("attribute_template", "attributes.id", "=", "attribute_template.attribute_id")
        ->leftJoin("templates", "attribute_template.template_id", "=", "templates.id")
        ->where('templates.id', $templateId)
        ->where('attribute_template.status', 1)
        ->groupBy('attributes.id')
        ->get();

        return $attributes;
    }
    
    /**
     * rules untuk assesment
     */
    public function requiredIf($templateId)
    {
        $attributes = $this->listingAttributes($templateId);
        $data = [];
        foreach ($attributes as $key => $attribute) {
            $data[$attribute->name] = ['required'];

            if($attribute->type == 'conditional_radio_prepend_number') {
                $data[$attribute->name] = ['required'];
                $data[$attribute->name. '_text'] = ['required_if:'.$attribute->name.', ==, "1"',
                function ($attribute, $value, $fail) {
                    if (!is_int($value) && $value) {
                        $fail($attribute . ' harus berupa numeric.');
                    } 
                },];
            }

            if($attribute->type == 'conditional_radio_prepend_string') {
                $data[$attribute->name] = ['required'];
                $data[$attribute->name. '_text'] = ['required_if:'.$attribute->name.', ==, "1"', function ($attribute, $value, $fail) {
                    if (!is_string($value) && $value) {
                        $fail($attribute . ' harus berupa string.');
                    } 
                },];
            }

            if($attribute->type == 'pernah_mondok') {
                $data[$attribute->name] = ['nullable'];
                $data['pernah_dirawat_simo'] = ['required'];
                $data['inap_ke_simo']= ['required_if:pernah_dirawat_simo,==,"1"'];
                $data['terakhir_dirawat_simo']= ['required_if:pernah_dirawat_simo,==,"1"'];
                $data['terakhir_dirawat_diruang_simo']= ['required_if:pernah_dirawat_simo,==,"1"'];
            }

            if($attribute->type == 'nyeri') {
                $data[$attribute->name] = ['required'];
                $data[$attribute->name.'_pilihan'] = ['required_with:'.$attribute->name.''];
            }

            if($attribute->type == 'fungsional') {
                $data[$attribute->name] = ['nullable'];
                $data['fungsional_makan'] = ['required'];
                $data['fungsional_mandi'] = ['required'];
                $data['fungsional_grooming'] = ['required'];
                $data['fungsional_dressing'] = ['required'];
                $data['fungsional_bowel'] = ['required'];
                $data['fungsional_bladder'] = ['required'];
                $data['fungsional_penggunaan_toilet'] = ['required'];
                $data['fungsional_transfer'] = ['required'];
                $data['fungsional_mobilitas'] = ['required'];
                $data['fungsional_naik_turun_tangga'] = ['required'];
                $data['fungsional_hasil'] = ['required'];
            }

            // info([str_contains($attribute->name, 'nutrisional') !== FALSE, strtolower($attribute->name)]);

            if(str_contains($attribute->name, 'nutrisional') !== FALSE) {
                $data[$attribute->name] = [''.$attribute->rules.''];
            }

            if($attribute->type == 'thoraks') {
                $data[$attribute->name] = ['nullable'];
                $data[$attribute->name. '_cor'] = ['required'];
                $data[$attribute->name. '_pulmo'] = ['required'];
            }

            if($attribute->type == 'operasi_yang_pernah_dialami') {
                $data[$attribute->name] = ['required'];
                $data[$attribute->name. '_jenis']= ['required_if:operasi_yang_pernah_dialami,==,"1"'];
                $data[$attribute->name. '_kapan']= ['required_if:operasi_yang_pernah_dialami,==,"1"'];
                $data[$attribute->name. '_komplikasi']= ['required_if:operasi_yang_pernah_dialami,==,"1"'];
            }

            if($attribute->type == 'laboratorium') {
                $data[$attribute->name] = ['nullable'];
            }

            // if($attribute->type == 'nutrisional_dewasa_penurunan_bb') {
            //     $data[$attribute->name] = ['required'];
            //     $data[$attribute->name. '_pilihan'] = ['required_if:'.$attribute->name.', ==, "ya"'];
            //     $data[$attribute->name. '_total_skor'] = ['required'];
            // }
        }

        return $data;
    }
}