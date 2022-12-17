<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeTemplate;
use App\Models\Template;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttributeTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $templates = Template::all();
        $attributes = Attribute::all();

        foreach ($templates as $key => $template) {
            foreach ($attributes as $keyAttribute => $attribute) {
                if ($keyAttribute < 19 && $template->id == 1) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Informasi Klinis',
                        'group_id' => 1,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 20 && $keyAttribute < 23 && $template->id == 1) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Informasi Klinis',
                        'group_id' => 1,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 29 && $keyAttribute < 32 && $template->id == 1) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Informasi Klinis',
                        'group_id' => 1,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 21 && $keyAttribute < 23 && $template->id == 2) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Status Fisik',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 22 && $keyAttribute < 28 && $template->id == 2) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Informasi Dasar',
                        'group_id' => 1,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 27 && $keyAttribute < 36 && $template->id == 2) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Status Fisik',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 34 && $keyAttribute < 44 && $template->id == 2) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Status Psikososial Spiritual Ekonomi',
                        'group_id' => 3,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 42 && $keyAttribute < 50 && $template->id == 2) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Rasa Nyeri',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 48 && $keyAttribute < 51 && $template->id == 2) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Fungsional',
                        'group_id' => 5,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 51 && $keyAttribute < 55 && $template->id == 2) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Fungsional',
                        'group_id' => 5,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 54 && $keyAttribute < 63 && $template->id == 2) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Resiko Nutrisional Untuk Pasien Dewasa / Ginekologi / Onkologi',
                        'group_id' => 6,
                        'status' => 1,
                        'rules' => 'nullable'
                    ]);
                } else if($keyAttribute > 62 && $keyAttribute < 71 && $template->id == 2) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Resiko Nutrisional Untuk Anak 1 Bulan sd. 14 Tahunan',
                        'group_id' => 7,
                        'status' => 1,
                        'rules' => 'nullable'
                    ]);
                } else if($keyAttribute > 70 && $keyAttribute < 78 && $template->id == 2) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Resiko Nutrisional Untuk Obtetri / Kehamilan / Nifas',
                        'group_id' => 8,
                        'status' => 1,
                        'rules' => 'nullable'
                    ]);
                } else if($keyAttribute > 77 && $keyAttribute < 79 && $template->id == 2) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Analisa Masalah Keperawatan Atau Kebidanan',
                        'group_id' => 9,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 78 && $keyAttribute < 80 && $template->id == 2) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Implementasi',
                        'group_id' => 11,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 139 && $template->id == 2) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Diagnosis Keperawatan',
                        'group_id' => 10,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 23 && $keyAttribute < 26 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Informasi Dasar',
                        'group_id' => 1,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 39 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Informasi Dasar',
                        'group_id' => 1,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 41 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Informasi Dasar',
                        'group_id' => 1,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 22 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Status Fisik',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 28 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Status Fisik',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 27 && $keyAttribute < 28 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Status Fisik',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 29 && $keyAttribute < 36 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Status Fisik',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 79 && $keyAttribute < 82 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Riwayat Sosial-Ekonomi Orang Tua',
                        'group_id' => 3,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 80 && $keyAttribute < 86 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Riwayat Perinatal',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 85 && $keyAttribute < 88 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Riwayat Kesehatan',
                        'group_id' => 5,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 27  && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Riwayat Kesehatan',
                        'group_id' => 5,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 86 && $keyAttribute < 93 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Kebutuhan Komunikasi / Pendidikan & Pengajaran',
                        'group_id' => 6,
                        'status' => 1,
                        'rules' => 'required'
                    ]);   
                } else if($keyAttribute > 43 && $keyAttribute < 50 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Rasa Nyeri',
                        'group_id' => 7,
                        'status' => 1,
                        'rules' => 'required'
                    ]); 
                } else if($keyAttribute == 93  && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Riwayat Imunisasi',
                        'group_id' => 8,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 62 && $keyAttribute < 71 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Skrining Nutrisi',
                        'group_id' => 9,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 93 && $keyAttribute < 108 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Riwayat Tumbuh Kembang',
                        'group_id' => 10,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 51 && $keyAttribute < 55 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Riwayat Jatuh',
                        'group_id' => 11,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 139 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Diagnosis Keperawatan',
                        'group_id' => 12,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 78 && $keyAttribute < 80 && $template->id == 3) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Implementasi',
                        'group_id' => 13,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 27 && $template->id == 4) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Informasi Dasar',
                        'group_id' => 1,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 22 && $template->id == 4) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Anamnesis',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 107 && $keyAttribute < 111 && $template->id == 4) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Anamnesis',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 29 && $keyAttribute < 36 && $template->id == 4) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 3,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 110 && $keyAttribute < 122 && $template->id == 4) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 3,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 121 && $keyAttribute < 122 && $template->id == 4) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Penunjang',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 122 && $template->id == 4) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Penunjang',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'nullable'
                    ]);
                } else if($keyAttribute > 122 && $keyAttribute < 130 && $template->id == 4) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Penunjang',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 1 && $template->id == 4) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 3,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 27 && $template->id == 5) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Informasi Dasar',
                        'group_id' => 1,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 22 && $template->id == 5) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Anamnesis',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 107 && $keyAttribute < 111 && $template->id == 5) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Anamnesis',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 29 && $keyAttribute < 36 && $template->id == 5) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 3,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 110 && $keyAttribute < 122 && $template->id == 5) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 3,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 1 && $template->id == 5) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 3,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 121 && $keyAttribute < 122 && $template->id == 5) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Penunjang',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 122 && $template->id == 5) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Penunjang',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'nullable'
                    ]);
                } else if($keyAttribute > 122 && $keyAttribute < 130 && $template->id == 5) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Penunjang',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 1 && $template->id == 6) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Status Fisik',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 21 && $keyAttribute < 23 && $template->id == 6) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Status Fisik',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 23 && $keyAttribute < 26 && $template->id == 6) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Informasi Dasar',
                        'group_id' => 1,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 27 && $keyAttribute < 29 && $template->id == 6) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Status Fisik',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 29 && $keyAttribute < 36 && $template->id == 6) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 3,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 39 && $template->id == 6) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Status Psikososial Spiritual Ekonomi',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 41 && $template->id == 6) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Status Psikososial Spiritual Ekonomi',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 42 && $keyAttribute < 50 && $template->id == 6) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Rasa Nyeri',
                        'group_id' => 5,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 51 && $keyAttribute < 55 && $template->id == 6) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Riwayat Jatuh',
                        'group_id' => 6,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 107 && $keyAttribute < 111 && $template->id == 6) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Anamnesis',
                        'group_id' => 8,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 110 && $keyAttribute < 113 && $template->id == 6) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 9,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 112 && $keyAttribute < 118 && $template->id == 6) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 9,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 119 && $template->id == 6) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 9,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 121 && $template->id == 6) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 9,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 27 && $template->id == 7) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Informasi Dasar',
                        'group_id' => 1,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 130 && $template->id == 7) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Informasi Dasar',
                        'group_id' => 1,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 22 && $template->id == 7) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Anamnesis',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 107 && $keyAttribute < 111 && $template->id == 7) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Anamnesis',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 131 && $template->id == 7) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Anamnesis',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 29 && $keyAttribute < 36 && $template->id == 7) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 3,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 110 && $keyAttribute < 122 && $template->id == 7) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 3,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 1 && $template->id == 7) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 3,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 121 && $keyAttribute < 122 && $template->id == 7) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Penunjang',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 122 && $template->id == 7) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Penunjang',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'nullable'
                    ]);
                } else if($keyAttribute > 122 && $keyAttribute < 130 && $template->id == 7) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Penunjang',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 136 && $template->id == 7) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Penunjang',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 131 && $keyAttribute < 136 && $template->id == 7) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Status Neurologis',
                        'group_id' => 5,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 136 && $keyAttribute < 139 && $template->id == 7) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Rencana Tata Laksana',
                        'group_id' => 6,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 27 && $template->id == 8) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Informasi Dasar',
                        'group_id' => 1,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 22 && $template->id == 8) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Anamnesis',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 107 && $keyAttribute < 111 && $template->id == 8) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Anamnesis',
                        'group_id' => 2,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 29 && $keyAttribute < 36 && $template->id == 8) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 3,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 110 && $keyAttribute < 122 && $template->id == 8) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 3,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 1 && $template->id == 8) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Fisik',
                        'group_id' => 3,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute > 121 && $keyAttribute < 122 && $template->id == 8) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Penunjang',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else if($keyAttribute == 122 && $template->id == 8) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Penunjang',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'nullable'
                    ]);
                } else if($keyAttribute > 122 && $keyAttribute < 130 && $template->id == 8) {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id,
                        'group_name' => 'Pemeriksaan Penunjang',
                        'group_id' => 4,
                        'status' => 1,
                        'rules' => 'required'
                    ]);
                } else {
                    AttributeTemplate::create([
                        'attribute_id' => $attribute->id,
                        'template_id' => $template->id
                    ]);           
                }
            }
        }
    }
}
