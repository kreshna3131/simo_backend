<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\Template;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('attributes')->insert([
            [
                'label' => 'Tanggal timbul gejala (onset)',
                'type' => 'date',
                'name' => 'tanggal_timbul_gejala',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Suhu badan',
                'type' => 'append_number',
                'name' => 'suhu_badan',
                'items' => '',
                'info' => '°C'
            ],
            [
                'label' => 'Riwayat demam',
                'type' => 'radio',
                'name' => 'riwayat_demam',
                'items' => '[{"label" : "Ya", "value" : "ya"}, {"label" : "Tidak", "value" : "tidak"}, {"label" : "Tidak Tahu", "value" : "tidak tahu"}]',
                'info' => ''
            ],
            [
                'label' => 'Batuk',
                'type' => 'radio',
                'name' => 'batuk',
                'items' => '[{"label" : "Ya", "value" : "ya"}, {"label" : "Tidak", "value" : "tidak"}, {"label" : "Tidak Tahu", "value" : "tidak tahu"}]',
                'info' => ''
            ],
            [
                'label' => 'Pilek',
                'type' => 'radio',
                'name' => 'pilek',
                'items' => '[{"label" : "Ya", "value" : "ya"}, {"label" : "Tidak", "value" : "tidak"}, {"label" : "Tidak Tahu", "value" : "tidak tahu"}]',
                'info' => ''
            ],
            [
                'label' => 'Sakit tenggorokan',
                'type' => 'radio',
                'name' => 'sakit_tenggorokan',
                'items' => '[{"label" : "Ya", "value" : "ya"}, {"label" : "Tidak", "value" : "tidak"}, {"label" : "Tidak Tahu", "value" : "tidak tahu"}]',
                'info' => ''
            ],
            [
                'label' => 'Sesak napas',
                'type' => 'radio',
                'name' => 'sesak_napas',
                'items' => '[{"label" : "Ya", "value" : "ya"}, {"label" : "Tidak", "value" : "tidak"}, {"label" : "Tidak Tahu", "value" : "tidak tahu"}]',
                'info' => ''
            ],
            [
                'label' => 'Sakit kepala',
                'type' => 'radio',
                'name' => 'sakit_kepala',
                'items' => '[{"label" : "Ya", "value" : "ya"}, {"label" : "Tidak", "value" : "tidak"}, {"label" : "Tidak Tahu", "value" : "tidak tahu"}]',
                'info' => ''
            ],
            [
                'label' => 'Lemah (malaise)',
                'type' => 'radio',
                'name' => 'lemah',
                'items' => '[{"label" : "Ya", "value" : "ya"}, {"label" : "Tidak", "value" : "tidak"}, {"label" : "Tidak Tahu", "value" : "tidak tahu"}]',
                'info' => ''
            ],
            [
                'label' => 'Nyeri otot',
                'type' => 'radio',
                'name' => 'nyeri_otot',
                'items' => '[{"label" : "Ya", "value" : "ya"}, {"label" : "Tidak", "value" : "tidak"}, {"label" : "Tidak Tahu", "value" : "tidak tahu"}]',
                'info' => ''
            ],
            [
                'label' => 'Mual atau muntah',
                'type' => 'radio',
                'name' => 'mual_muntah',
                'items' => '[{"label" : "Ya", "value" : "ya"}, {"label" : "Tidak", "value" : "tidak"}, {"label" : "Tidak Tahu", "value" : "tidak tahu"}]',
                'info' => ''
            ],
            [
                'label' => 'Nyeri perut',
                'type' => 'radio',
                'name' => 'nyeri_perut',
                'items' => '[{"label" : "Ya", "value" : "ya"}, {"label" : "Tidak", "value" : "tidak"}, {"label" : "Tidak Tahu", "value" : "tidak tahu"}]',
                'info' => ''
            ],
            [
                'label' => 'Diare',
                'type' => 'radio',
                'name' => 'diare',
                'items' => '[{"label" : "Ya", "value" : "ya"}, {"label" : "Tidak", "value" : "tidak"}, {"label" : "Tidak Tahu", "value" : "tidak tahu"}]',
                'info' => ''
            ],
            [
                'label' => 'Gangguan penciuman',
                'type' => 'radio',
                'name' => 'gangguan_penciuman',
                'items' => '[{"label" : "Ya", "value" : "ya"}, {"label" : "Tidak", "value" : "tidak"}, {"label" : "Tidak Tahu", "value" : "tidak tahu"}]',
                'info' => ''
            ],
            [
                'label' => 'Gangguan perasa',
                'type' => 'radio',
                'name' => 'gangguan_perasa',
                'items' => '[{"label" : "Ya", "value" : "ya"}, {"label" : "Tidak", "value" : "tidak"}, {"label" : "Tidak Tahu", "value" : "tidak tahu"}]',
                'info' => ''
            ],
            [
                'label' => 'Gangguan lainnya',
                'type' => 'textarea',
                'name' => 'gangguan_lainnya',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Apakah pernah dirawat di RS?',
                'type' => 'radio',
                'name' => 'pernah_dirawat',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Apakah pernah disuntik vaksin?',
                'type' => 'conditional_radio_prepend_number',
                'name' => 'pernah_divaksin',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => 'Berapa kali?'
            ],
            [
                'label' => 'Pernah dilakukan pemeriksaan rapid / swab?',
                'type' => 'conditional_radio_prepend_string',
                'name' => 'rapid_swab_test',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => 'Kapan?'
            ],
            [
                'label' => 'Tekanan darah',
                'type' => 'text',
                'name' => 'covid_td',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Nadi',
                'type' => 'number',
                'name' => 'covid_n',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Suhu sekarang',
                'type' => 'number',
                'name' => 'covid_s',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Keluhan',
                'type' => 'textarea',
                'name' => 'keluhan',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Poliklinik',
                'type' => 'text',
                'name' => 'poliklinik',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Kiriman dari',
                'type' => 'radio',
                'name' => 'kiriman_dari',
                'items' => '[{"label" : "RS Lain", "value" : "rs lain"}, {"label" : "Puskesmas", "value" : "puskesmas"}, {"label" : "Datang Sendiri", "value" : "datang sendiri"}]',
                'info' => ''
            ],
            [
                'label' => 'Pembayaran',
                'type' => 'radio',
                'name' => 'pembayaran',
                'items' => '[{"label" : "Umum", "value" : "umum"}, {"label" : "BPJS", "value" : "bpjs"}, {"label" : "Lain-Lain", "value" : "lain-lain"}]',
                'info' => ''
            ],
            [
                'label' => 'Pernah mondok di RSUD Simo?',
                'type' => 'pernah_mondok',
                'name' => 'pernah_mondok',
                'items' => '[{"label" : "Pernah", "value" : "1"}, {"label" : "Belum", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Riwayat alergi',
                'type' => 'conditional_radio_prepend_string',
                'name' => 'riwayat_alergi',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => 'Jelaskan'
            ],
            [
                'label' => 'Kesadaran',
                'type' => 'text',
                'name' => 'kesadaran',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'GCS',
                'type' => 'number',
                'name' => 'gcs',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Tekanan darah',
                'type' => 'append_string',
                'name' => 'tekanan_darah',
                'items' => '',
                'info' => 'mmHg'
            ],
            [
                'label' => 'Frekuensi nadi',
                'type' => 'append_number',
                'name' => 'frekuensi_nadi',
                'items' => '',
                'info' => 'X/menit'
            ],
            [
                'label' => 'Frekuensi napas',
                'type' => 'append_number',
                'name' => 'frekuensi_napas',
                'items' => '',
                'info' => 'X/menit'
            ],
            [
                'label' => 'Keadaan Umum',
                'type' => 'text',
                'name' => 'keadaan_umum',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Berat badan',
                'type' => 'append_number',
                'name' => 'berat_badan',
                'items' => '',
                'info' => 'g/kg'
            ],
            [
                'label' => 'Tinggi badan',
                'type' => 'append_number',
                'name' => 'tinggi_badan',
                'items' => '',
                'info' => 'cm'
            ],

            [
                'label' => 'Psikologi sosial',
                'type' => 'radio',
                'name' => 'psikologi',
                'items' => '[{"label" : "Tenang", "value" : "tenang"}, {"label" : "Cemas", "value" : "cemas"}, {"label" : "Takut", "value" : "takut"}, {"label" : "Marah", "value" : "marah"}, {"label" : "Sedih", "value" : "sedih"}, {"label" : "Ada resiko mencederai diri sendiri", "value" : "ada resiko mencederai diri sendiri"}]',
                'info' => ''
            ],
            [
                'label' => 'Sosial (suku)',
                'type' => 'text',
                'name' => 'suku',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Tempat tinggal',
                'type' => 'radio',
                'name' => 'tempat_tinggal',
                'items' => '[{"label" : "Rumah Pribadi", "value" : "rumah pribadi"}, {"label" : "Kontrakan", "value" : "kontrakan"}, {"label" : "Rumah Keluarga", "value" : "rumah keluarga"}, {"label" : "Panti Jompo", "value" : "panti jompo"}]',
                'info' => ''
            ],
            [
                'label' => 'Ekonomi (pekerjaan)',
                'type' => 'text',
                'name' => 'pekerjaan',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Penanggung jawab pembayaran',
                'type' => 'text',
                'name' => 'penanggung_jawab_pembayaran',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Agama',
                'type' => 'text',
                'name' => 'agama',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Kebutuhan spiritual khusus',
                'type' => 'conditional_radio_prepend_string',
                'name' => 'kebutuhan_spiritual_khusus',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => 'Sebutkan'
            ],
            [
                'label' => 'Memerlukan kerohanian',
                'type' => 'conditional_radio_prepend_string',
                'name' => 'memerlukan_rohaniawan',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => 'Sebutkan'
            ],
            [
                'label' => 'Berapa lama dan skala nyeri',
                'type' => 'nyeri',
                'name' => 'nyeri',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Pencetus (terjadi nyeri saat)',
                'type' => 'text',
                'name' => 'nyeri_pencetus',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Kualitas',
                'type' => 'radio',
                'name' => 'nyeri_kualitas',
                'items' => '[{"label" : "Tekanan", "value" : "tekanan"}, {"label" : "Terbakar", "value" : "terbakar"}, {"label" : "Tajam Tusukan", "value" : "tajam tusukan"}]',
                'info' => ''
            ],
            [
                'label' => 'Lokasi',
                'type' => 'text',
                'name' => 'nyeri_lokasi',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Skala (berdasarkan alat pengkajian nyeri)',
                'type' => 'number',
                'name' => 'nyeri_skala',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Durasi',
                'type' => 'radio',
                'name' => 'nyeri_durasi',
                'items' => '[{"label" : "Intermediet", "value" : "intermediet"}, {"label" : "Terus Menerus", "value" : "terus menerus"}]',
                'info' => ''
            ],
            [
                'label' => 'Fungsional',
                'type' => 'fungsional',
                'name' => 'fungsional',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Bila ketergantungan total, di kolaborasikan dengan DPJP',
                'type' => 'text',
                'name' => 'ketergantungan_total',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Menggunakan alat bantu (kruk / kursi roda / berpegangan / orang lain)',
                'type' => 'radio',
                'name' => 'alat_bantu',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Gaya berjalan',
                'type' => 'radio',
                'name' => 'gaya_berjalan',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Penatalaksanaan',
                'type' => 'radio',
                'name' => 'penatalaksanaan',
                'items' => '[{"label" : "Edukasi", "value" : "edukasi"}, {"label" : "Pemasangan Kalung", "value" : "pemasangan kalung"}]',
                'info' => ''
            ],
            [
                'label' => 'Berat badan (untuk dewasa)',
                'type' => 'append_number',
                'name' => 'nutrisional_dewasa_bb',
                'items' => 'Kg',
                'info' => ''
            ],
            [
                'label' => 'Tinggi badan (untuk dewasa)',
                'type' => 'append_number',
                'name' => 'nutrisional_dewasa_tb',
                'items' => '',
                'info' => 'cm'
            ],
            [
                'label' => 'IMT (untuk dewasa)',
                'type' => 'append_number',
                'name' => 'nutrisional_dewasa_imt',
                'items' => '',
                'info' => 'BB/TB(m)'
            ],
            [
                'label' => 'LiLA (untuk dewasa)',
                'type' => 'append_number',
                'name' => 'nutrisional_dewasa_lila',
                'items' => '',
                'info' => 'cm'
            ],
            [
                'label' => 'Apakah pasien mengalami penurunan BB dalam 6 bulan terakhir?',
                'type' => 'nutrisional_dewasa_penurunan_bb',
                'name' => 'nutrisional_dewasa_penurunan_bb',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Apakah asupan makanan berkurang karena tidak ada nafsu makan (untuk dewasa)?',
                'type' => 'radio',
                'name' => 'nutrisional_dewasa_nafsu_makan',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Pasien dengan diagnosa khusus (DM / CKD / Infeksi Kronis / lainnya)',
                'type' => 'text',
                'name' => 'nutrisional_dewasa_diagnosa',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Kesimpulan beresiko malnutrisi (untuk dewasa)',
                'type' => 'radio',
                'name' => 'nutrisional_dewasa_kesimpulan',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Berat badan (untuk anak)',
                'type' => 'append_number',
                'name' => 'nutrisional_anak_bb',
                'items' => '',
                'info' => 'Kg'
            ],
            [
                'label' => 'Tinggi badan (untuk anak)',
                'type' => 'append_number',
                'name' => 'nutrisional_anak_tb',
                'items' => '',
                'info' => 'cm'
            ],
            [
                'label' => 'Apakah pasien tampak kurus?',
                'type' => 'radio',
                'name' => 'nutrisional_anak_kurus',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Apakah terdapat penurunan BB selama 1 bulan terakhir?',
                'type' => 'radio',
                'name' => 'nutrisional_anak_penurunan_bb',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Untuk bayi < 1 tahun : BB tidak naik selama 3 bulan terakhir?',
                'type' => 'radio',
                'name' => 'nutrisional_bayi_penurunan_bb',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Apakah terdapat salah satu dari kondisi berikut? (Diare, asupan makanan menurun)',
                'type' => 'radio',
                'name' => 'nutrisional_anak_kondisi',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Apakah terdapat penyakit / keadaan yang mengakibatkan pasien beresiko malnutrisi?',
                'type' => 'radio',
                'name' => 'nutrisional_anak_penyakit',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Kesimpulan beresiko malnutrisi (untuk anak)',
                'type' => 'radio',
                'name' => 'nutrisional_anak_kesimpulan',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Berat badan (untuk ibu hamil)',
                'type' => 'append_number',
                'name' => 'nutrisional_hamil_bb',
                'items' => '',
                'info' => 'Kg'
            ],
            [
                'label' => 'Tinggi badan (untuk ibu hamil)',
                'type' => 'append_number',
                'name' => 'nutrisional_hamil_tb',
                'items' => '',
                'info' => 'cm'
            ],
            [
                'label' => 'LiLA (untuk ibu hamil)',
                'type' => 'append_number',
                'name' => 'nutrisional_hamil_lila',
                'items' => '',
                'info' => 'cm'
            ],
            [
                'label' => 'Apakah asupan makanan berkurang karena tidak ada nafsu makan (untuk hamil)?',
                'type' => 'radio',
                'name' => 'nutrisional_hamil_nafsu_makan',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Ada gangguan metabolisme (DM; gangguan fungsi teroid infeksi kronis seperti: HIV / AIDS, TB, LUPUS, lainnya)',
                'type' => 'conditional_radio_prepend_string',
                'name' => 'nutrisional_hamil_metabolisme',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => 'Sebutkan'
            ],
            [
                'label' => 'Ada pertambahan BB yang kurang atau lebih selama kehamilan',
                'type' => 'radio',
                'name' => 'nutrisional_hamil_pertambahan_bb',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Nilai HB, 11 g/di atau HCT, 30%',
                'type' => 'radio',
                'name' => 'nutrisional_hamil_nilai_hb',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Analisa masalah keperawatan / kebidanan',
                'type' => 'analisa_keperawatan',
                'name' => 'analisa_keperawatan',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Implementasi',
                'type' => 'textarea',
                'name' => 'implementasi',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Pasien Dirawat Oleh',
                'type' => 'radio',
                'name' => 'pasien_dirawat_oleh',
                'items' => '[{"label" : "Orang Tua", "value" : "orang tua"}, {"label" : "Kakek / Nenek / Saudara", "value" : "wali"}, {"label" : "Panti Asuhan", "value" : "panti asuhan"}]',
                'info' => ''
            ],
            [
                'label' => 'Pekerjaan Orang Tua',
                'type' => 'text',
                'name' => 'pekerjaan_orang_tua',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Lama kehamilan',
                'type' => 'number',
                'name' => 'lama_kehamilan',
                'items' => '',
                'info' => 'bln/mg'
            ],
            [
                'label' => 'Komplikasi Kehamilan',
                'type' => 'conditional_radio_prepend_string',
                'name' => 'komplikasi_kehamilan',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => 'Jelaskan'
            ],
            [
                'label' => 'Riwayat Persalinan',
                'type' => 'radio',
                'name' => 'riwayat_kehamilan',
                'items' => '[{"label" : "Spontan", "value" : "spontan"}, {"label" : "Sectio", "value" : "sectio"}, {"label" : "Vaccum Extraksi", "value" : "vaccum extraksi"}, {"label" : "Forcef Extraksi", "value" : "forcef extraksi"}]',
                'info' => ''
            ],
            [
                'label' => 'Penyulit Kehamilan',
                'type' => 'conditional_radio_prepend_string',
                'name' => 'penyulit_kehamilan',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => 'Sebutkan'
            ],
            [
                'label' => 'Pengobatan saat ini',
                'type' => 'conditional_radio_prepend_string',
                'name' => 'pengobatan_saat_ini',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => 'Jelaskan'
            ],
            [
                'label' => 'Pernah Operasi',
                'type' => 'operasi_yang_pernah_dialami',
                'name' => 'operasi_yang_pernah_dialami',
                'items' => '[{"label" : "Pernah", "value" : "1"}, {"label" : "Belum", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Bicara',
                'type' => 'conditional_radio_prepend_string',
                'name' => 'bicara',
                'items' => '[{"label" : "Gangguan Bicara", "value" : "1"}, {"label" : "Normal", "value" : "0"}]',
                'info' => 'Jelaskan'
            ],
            [
                'label' => 'Perlu Penterjemah',
                'type' => 'conditional_radio_prepend_string',
                'name' => 'perlu_penterjemah',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => 'Sebutkan'
            ],
            [
                'label' => 'Bahasa Isyarat',
                'type' => 'radio',
                'name' => 'bahasa_isyarat',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Hambatan Belajar',
                'type' => 'radio',
                'name' => 'hambatan_belajar',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Tingkat Pendidikan',
                'type' => 'dropdown',
                'name' => 'tingkatan_pendidikan',
                'items' => '[{"label" : "SD", "value" : "SD"}, {"label" : "SMP", "value" : "SMP"}, {"label" : "SMA/SMK", "value" : "SMA/SMK"}, {"label" : "S1", "value" : "S1"}, {"label" : "S2", "value" : "S2"}, {"label" : "S3", "value" : "S3"}]',
                'info' => ''
            ],
            [
                'label' => 'Riwayat Imunisasi',
                'type' => 'riwayat_imunisasi',
                'name' => 'riwayat_imunisasi',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Lingkar Kepala Saat Lahir',
                'type' => 'append_number',
                'name' => 'lingkar_kepala',
                'items' => '',
                'info' => 'cm'
            ],
            [
                'label' => 'Berat Badan Saat Lahir',
                'type' => 'append_number',
                'name' => 'berat_badan_saat_lahir',
                'items' => '',
                'info' => 'gram'
            ],
            [
                'label' => 'Tinggi Badan Saat Lahir',
                'type' => 'append_number',
                'name' => 'tinggi_badan_saat_lahir',
                'items' => '',
                'info' => 'cm'
            ],
            [
                'label' => 'ASI Sampai Umur',
                'type' => 'append_number',
                'name' => 'asi_sampai_umur',
                'items' => '',
                'info' => 'bln/th'
            ],
            [
                'label' => 'Susu Formula Mulai',
                'type' => 'append_number',
                'name' => 'susu_formula_mulai',
                'items' => '',
                'info' => 'bln/th'
            ],
            [
                'label' => 'Makanan Tambahan',
                'type' => 'append_number',
                'name' => 'makanan_tambahan',
                'items' => '',
                'info' => 'bln/th'
            ],
            [
                'label' => 'Tengkurap',
                'type' => 'append_number',
                'name' => 'tengkurap',
                'items' => '',
                'info' => 'bln'
            ],
            [
                'label' => 'Duduk',
                'type' => 'append_number',
                'name' => 'duduk',
                'items' => '',
                'info' => 'bln'
            ],
            [
                'label' => 'Merangkak',
                'type' => 'append_number',
                'name' => 'merangkak',
                'items' => '',
                'info' => 'bln'
            ],
            [
                'label' => 'Berdiri',
                'type' => 'append_number',
                'name' => 'berdiri',
                'items' => '',
                'info' => 'bln'
            ],
            [
                'label' => 'Berjalan',
                'type' => 'append_number',
                'name' => 'berjalan',
                'items' => '',
                'info' => 'bln'
            ],
            [
                'label' => 'Masalah Neonatus',
                'type' => 'conditional_radio_prepend_string',
                'name' => 'masalah_neonatus',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => 'Sebutkan'
            ],
            [
                'label' => 'Jaundice/ RDS/ PJB/ Kelainan kongenita',
                'type' => 'conditional_radio_prepend_string',
                'name' => 'kelainan_kongenita',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => 'Jelaskan'
            ],
            [
                'label' => 'Keluhan tumbuh kembang sekarang',
                'type' => 'text',
                'name' => 'keluhan_tumbuh_tembang',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Riwayat Penyakit Sekarang',
                'type' => 'text',
                'name' => 'riwayat_penyakit_sekarang',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Riwayat Penyakit Dahulu',
                'type' => 'text',
                'name' => 'riwayat_penyakit_dahulu',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Riwayat Penyakit Keluarga',
                'type' => 'text',
                'name' => 'riwayat_penyakit_keluarga',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Tindakan Resusitasi',
                'type' => 'text',
                'name' => 'tindakan_resusitasi',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'GDS',
                'type' => 'append_number',
                'name' => 'gds',
                'items' => '',
                'info' => 'mg/dl'
            ],
            [
                'label' => 'Kepala',
                'type' => 'text',
                'name' => 'kepala',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Mata',
                'type' => 'text',
                'name' => 'mata',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Mulut',
                'type' => 'text',
                'name' => 'mulut',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Leher',
                'type' => 'text',
                'name' => 'leher',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Thoraks',
                'type' => 'thoraks',
                'name' => 'thoraks',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Abdomen',
                'type' => 'text',
                'name' => 'abdomen',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Extremitas',
                'type' => 'text',
                'name' => 'extremitas',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Anus - Genitalia',
                'type' => 'text',
                'name' => 'anus_genitalia',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Lain - lain',
                'type' => 'text',
                'name' => 'lain_lain',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Laboratorium',
                'type' => 'laboratorium',
                'name' => 'laboratorium_id',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'EKG',
                'type' => 'text',
                'name' => 'ekg',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'X-Ray',
                'type' => 'text',
                'name' => 'xray',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Diagnosis Kerja',
                'type' => 'text',
                'name' => 'diagnosis_kerja',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Diagnosis Banding',
                'type' => 'text',
                'name' => 'diagnosis_banding',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Rencana Terapi',
                'type' => 'text',
                'name' => 'rencana_terapi',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Rencana Tindak Lanjut',
                'type' => 'rencana_tindak_lanjut',
                'name' => 'rencana_tindak_lanjut',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Edukasi Pasien',
                'type' => 'text',
                'name' => 'edukasi_pasien',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Risiko Tinggi',
                'type' => 'radio',
                'name' => 'risiko_tinggi',
                'items' => '[{"label" : "Ya", "value" : "1"}, {"label" : "Tidak", "value" : "0"}]',
                'info' => ''
            ],
            [
                'label' => 'Riwayat Pengobatan ( Obat yang dikonsumsi)',
                'type' => 'text',
                'name' => 'riwayat_pengobatan',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Kepala (Neurologis)',
                'type' => 'neurologis_kepala',
                'name' => 'neurologis_kepala',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Leher (Neurologis)',
                'type' => 'neurologis_leher',
                'name' => 'neurologis_leher',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Vertebra (Neurologis)',
                'type' => 'text',
                'name' => 'neurologis_vertebra',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Extrimitas (Neurologis)',
                'type' => 'neurologis_extrimitas',
                'name' => 'neurologis_extrimitas',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Elektromedik',
                'type' => 'text',
                'name' => 'elektromedik',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Tindakan',
                'type' => 'text',
                'name' => 'tindakan',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Medika Mentosa',
                'type' => 'text',
                'name' => 'medika_mentosa',
                'items' => '',
                'info' => ''
            ],
            [
                'label' => 'Diagnosis Keperawatan',
                'type' => 'text',
                'name' => 'diagnosis_keperawatan',
                'items' => '',
                'info' => ''
            ],
        ]);
    }
}
