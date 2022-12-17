<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('templates')->insert([
            [
                'type' => 'Covid',
                'name' => 'Deteksi Dini Kewaspadaan Terhadap COVID 19',
                'visibility' =>1
            ],
            [
                'type' => 'Umum Dewasa',
                'name' => 'Assesment Awal Keperawatan Rawat Jalan',
                'visibility' => 1
            ],
            [
                'type' => 'Umum Anak',
                'name' => 'Assesment Awal Keperawatan Pasien Anak Rawat Jalan',
                'visibility' => 1
            ],
            [
                'type' => 'Spesialis Anak',
                'name' => 'Assesment Awal Medis Anak',
                'visibility' => 1
            ],
            [
                'type' => 'Spesialis Penyakit Dalam',
                'name' => 'Assesment Awal Medis Penyakit Dalam',
                'visibility' => 1
            ],
            [
                'type' => 'Global',
                'name' => 'Assesment Global',
                'visibility' => 1
            ],
            [
                'type' => 'Spesialis Syaraf',
                'name' => 'Assesment Awal Medis Syaraf',
                'visibility' => 1
            ],
            [
                'type' => 'Spesialis Paru',
                'name' => 'Assesment Awal Medis Paru',
                'visibility' => 1
            ],
        ]);
    }
}
