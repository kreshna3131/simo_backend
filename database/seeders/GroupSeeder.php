<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('groups')->insert([
            [
                'name' => 'Informasi Klinis',
            ],
            [
                'name' => 'Informasi Dasar',
            ],
            [
                'name' => 'Status Fisik',
            ],
            [
                'name' => 'Status Psikososial Spiritual Ekonomi',
            ],
            [
                'name' => 'Fungsional',
            ],
            [
                'name' => 'Resiko Nutrisional untuk Pasien Dewasa / Ginekologi / Onkologi',
            ],
            [
                'name' => 'Resiko Nutrisional Untuk Anak 1 Bulan sd. 14 Tahuan',
            ],
            [
                'name' => 'Resiko Nutrisional Untuk Obtetri / Kehamilan / Nifas',
            ],
            [
                'name' => 'Analisa Masalah Keperawatan atau Kebidanan',
            ],
            [
                'name' => 'Diagnosis Keperawatan',
            ],
            [
                'name' => 'Implementasi',
            ],
            [
                'name' => 'Riwayat Sosial-Ekonomi Orang Tua',
            ],
            [
                'name' => 'Riwayat Perinatal',
            ],
            [
                'name' => 'Riwayat Kesehatan',
            ],
            [
                'name' => 'Kebutuhan Komunikasi / Pendidikan & Pengajaran',
            ],
            [
                'name' => 'Rasa Nyeri',
            ],
            [
                'name' => 'Riwayat Imunisasi',
            ],
            [
                'name' => 'Skrining Nutrisi',
            ],
            [
                'name' => 'Riwayat Tumbuh Kembang',
            ],
            [
                'name' => 'Resiko Jatuh',
            ],
            [
                'name' => 'Anamnesis',
            ],
            [
                'name' => 'Pemeriksaan Fisik',
            ],
            [
                'name' => 'Status Neurologis',
            ],
            [
                'name' => 'Pemeriksaan Penunjang',
            ],
            [
                'name' => 'Rencana Tata Laksana',
            ],
        ]);
    }
}
