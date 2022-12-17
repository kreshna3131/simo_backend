<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_assesments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('assesment_id')->nullable();
            $table->bigInteger('template_id')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->datetime('tanggal_timbul_gejala')->nullable();
            $table->integer('suhu_badan')->nullable();
            $table->enum('riwayat_demam', ['ya','tidak', 'tidak tahu'])->nullable();
            $table->enum('batuk', ['ya','tidak', 'tidak tahu'])->nullable();
            $table->enum('pilek', ['ya','tidak', 'tidak tahu'])->nullable();
            $table->enum('sakit_tenggorokan', ['ya','tidak', 'tidak tahu'])->nullable();
            $table->enum('sesak_napas', ['ya','tidak', 'tidak tahu'])->nullable();
            $table->enum('sakit_kepala', ['ya','tidak', 'tidak tahu'])->nullable();
            $table->enum('lemah', ['ya','tidak', 'tidak tahu'])->nullable();
            $table->enum('nyeri_otot', ['ya','tidak', 'tidak tahu'])->nullable();
            $table->enum('mual_muntah', ['ya','tidak', 'tidak tahu'])->nullable();
            $table->enum('nyeri_perut', ['ya','tidak', 'tidak tahu'])->nullable();
            $table->enum('diare', ['ya','tidak', 'tidak tahu'])->nullable();
            $table->enum('gangguan_penciuman', ['ya','tidak', 'tidak tahu'])->nullable();
            $table->enum('gangguan_perasa', ['ya','tidak', 'tidak tahu'])->nullable();
            $table->string('gangguan_lainnya')->nullable();
            $table->boolean('pernah_dirawat')->nullable();
            $table->boolean('pernah_divaksin')->nullable();
            $table->integer('pernah_divaksin_text')->nullable();
            $table->boolean('rapid_swab_test')->nullable();
            $table->string('rapid_swab_test_text')->nullable();
            $table->string('covid_td')->nullable();
            $table->string('covid_n')->nullable();
            $table->string('covid_s')->nullable();
            $table->boolean('riwayat_alergi')->nullable();
            $table->string('riwayat_alergi_text')->nullable();
            $table->string('poliklinik')->nullable();
            $table->string('keluhan')->nullable();
            $table->string('kesadaran')->nullable();
            $table->integer('gcs')->nullable();
            $table->string('tekanan_darah')->nullable();
            $table->integer('frekuensi_nadi')->nullable();
            $table->integer('frekuensi_napas')->nullable();
            $table->string('keadaan_umum')->nullable();
            $table->decimal('berat_badan')->nullable();
            $table->integer('tinggi_badan')->nullable();
            $table->enum('kiriman_dari', ['rs lain', 'puskesmas', 'datang sendiri'])->nullable();
            $table->enum('pembayaran', ['umum', 'bpjs', 'lain-lain'])->nullable();
            $table->boolean('pernah_dirawat_simo')->nullable();
            $table->integer('inap_ke_simo')->nullable();
            $table->datetime('terakhir_dirawat_simo')->nullable();
            $table->string('terakhir_dirawat_diruang_simo')->nullable();
            $table->enum('psikologi', ['tenang', 'cemas', 'takut', 'marah', 'sedih', 'ada resiko mencederai diri sendiri'])->nullable();
            $table->string('suku')->nullable();
            $table->enum('tempat_tinggal', ['rumah pribadi', 'kontrakan', 'rumah keluarga', 'panti jompo'])->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('penanggung_jawab_pembayaran')->nullable();
            $table->string('agama')->nullable();
            $table->boolean('kebutuhan_spiritual_khusus')->nullable();
            $table->string('kebutuhan_spiritual_khusus_text')->nullable();
            $table->boolean('memerlukan_rohaniawan')->nullable();
            $table->string('memerlukan_rohaniawan_text')->nullable();
            $table->string('nyeri')->nullable();
            $table->string('nyeri_pilihan')->nullable();
            $table->string('nyeri_pencetus')->nullable();
            $table->enum('nyeri_kualitas', ['tekanan', 'terbakar', 'tajam tusukan'])->nullable();
            $table->string('nyeri_lokasi')->nullable();
            $table->integer('nyeri_skala')->nullable();
            $table->enum('nyeri_durasi', ['intermediet', 'terus menerus'])->nullable();
            $table->enum('fungsional_makan', ['0 = tidak mampu', '1 = butuh bantuan memotong, mengoles mentega dll', '2 = mandiri'])->nullable();
            $table->enum('fungsional_mandi', ['0 = tergantung orang lain', '1 = mandiri'])->nullable();
            $table->enum('fungsional_grooming', ['0 = membutuhkan bantuan orang lain', '1 = mandiri dalam perawatan muka, rambut, gigi dan bercukur'])->nullable();
            $table->enum('fungsional_dressing', ['0 = tergantung orang lain', '1 = sebagian dibantu (misal mengancing baju)', '2 = mandiri'])->nullable();
            $table->enum('fungsional_bowel', ['0 = inkontinensia atau pakai kateter dan tidak terkontrol', '1 = kadang inkontinensia (maks, 1x24 jam)', '2 = kontinensia (teratur untuk lebih dari 7 hari)'])->nullable();
            $table->enum('fungsional_bladder', ['0 = inkontinensia (tidak teratur atau perlu enema)', '1 = kadang inkontinensia (sekali seminggu)', '2 = kontinensia (teratur)'])->nullable();
            $table->enum('fungsional_penggunaan_toilet', ['0 = tergantung bantuan orang lain', '1 = membutuhkan bantuan, tapi dapat melakukan beberapa hal sendiri', '2 = mandiri'])->nullable();
            $table->enum('fungsional_transfer', ['0 = tidak mampu', '1 = butuh bantuan untuk bisa duduk (2 orang)', '2 = bantuan kecil (1 orang)', '3 = mandiri'])->nullable();
            $table->enum('fungsional_mobilitas', ['0 = immobile (tidak mampu)', '1 = menggunakan kursi roda', '2 = berjalan dengan bantuan orang lain', '3 = mandiri (meskipun menggunakan alat bantu seperti tongkat)'])->nullable();
            $table->enum('fungsional_naik_turun_tangga', ['0 = tidak mampu', '1 = membutuhkan bantuan (atau alat bantu)', '2 = mandiri'])->nullable();
            $table->enum('fungsional_hasil', ['20 : mandiri', '5-8 : ketergantungan berat', '12-19 : ketergantungan ringan', '0-4 : ketergantungan total', '9-11 : ketergantungan sedang'])->nullable();
            $table->text('ketergantungan_total')->nullable();
            $table->boolean('alat_bantu')->nullable();
            $table->boolean('gaya_berjalan')->nullable();
            $table->enum('penatalaksanaan', ['edukasi', 'pemasangan kalung'])->nullable();
            $table->decimal('nutrisional_dewasa_bb')->nullable();
            $table->integer('nutrisional_dewasa_tb')->nullable();
            $table->integer('nutrisional_dewasa_imt')->nullable();
            $table->integer('nutrisional_dewasa_lila')->nullable();
            $table->enum('nutrisional_dewasa_penurunan_bb', ['tidak ada', 'tidak yakin', 'ya'])->nullable();
            $table->string('nutrisional_dewasa_penurunan_bb_pilihan')->nullable();
            $table->integer('nutrisional_dewasa_penurunan_bb_total_skor')->nullable();
            $table->boolean('nutrisional_dewasa_nafsu_makan')->nullable();
            $table->string('nutrisional_dewasa_diagnosa')->nullable();
            $table->boolean('nutrisional_dewasa_kesimpulan')->nullable();
            $table->decimal('nutrisional_anak_bb')->nullable();
            $table->integer('nutrisional_anak_tb')->nullable();
            $table->boolean('nutrisional_anak_kurus')->nullable();
            $table->boolean('nutrisional_anak_penurunan_bb')->nullable();
            $table->boolean('nutrisional_bayi_penurunan_bb')->nullable();
            $table->boolean('nutrisional_anak_kondisi')->nullable();
            $table->boolean('nutrisional_anak_penyakit')->nullable();
            $table->boolean('nutrisional_anak_kesimpulan')->nullable();
            $table->decimal('nutrisional_hamil_bb')->nullable();
            $table->integer('nutrisional_hamil_tb')->nullable();
            $table->integer('nutrisional_hamil_lila')->nullable();
            $table->boolean('nutrisional_hamil_nafsu_makan')->nullable();
            $table->boolean('nutrisional_hamil_metabolisme')->nullable();
            $table->string('nutrisional_hamil_metabolisme_text')->nullable();
            $table->boolean('nutrisional_hamil_pertambahan_bb')->nullable();
            $table->boolean('nutrisional_hamil_nilai_hb')->nullable();
            $table->text('analisa_keperawatan')->nullable();
            $table->text('implementasi')->nullable();
            $table->enum('pasien_dirawat_oleh', ['orang tua', 'wali', 'panti asuhan'])->nullable();
            $table->string('pekerjaan_orang_tua')->nullable();
            $table->integer('lama_kehamilan')->nullable();
            $table->boolean('komplikasi_kehamilan')->nullable();
            $table->string('komplikasi_kehamilan_text')->nullable();
            $table->enum('riwayat_kehamilan', ['spontan', 'sectio', 'vaccum extraksi', 'forcef extraksi'])->nullable();
            $table->boolean('penyulit_kehamilan')->nullable();
            $table->string('penyulit_kehamilan_text')->nullable();
            $table->boolean('pengobatan_saat_ini')->nullable();
            $table->string('pengobatan_saat_ini_text')->nullable();
            $table->boolean('operasi_yang_pernah_dialami')->nullable();
            $table->string('operasi_yang_pernah_dialami_jenis')->nullable();
            $table->string('operasi_yang_pernah_dialami_kapan')->nullable();
            $table->string('operasi_yang_pernah_dialami_komplikasi')->nullable();
            $table->boolean('bicara')->nullable();
            $table->string('bicara_text')->nullable();
            $table->boolean('perlu_penterjemah')->nullable();
            $table->string('perlu_penterjemah_text')->nullable();
            $table->boolean('bahasa_isyarat')->nullable();
            $table->boolean('hambatan_belajar')->nullable();
            $table->enum('tingkatan_pendidikan', ['SD', 'SMP', 'SMA/SMK', 'S1', 'S2', 'S3'])->nullable();
            $table->text('riwayat_imunisasi')->nullable();
            $table->integer('lingkar_kepala')->nullable();
            $table->decimal('berat_badan_saat_lahir')->nullable();
            $table->integer('tinggi_badan_saat_lahir')->nullable();
            $table->integer('asi_sampai_umur')->nullable();
            $table->integer('susu_formula_mulai')->nullable();
            $table->integer('makanan_tambahan')->nullable();
            $table->integer('tengkurap')->nullable();
            $table->integer('duduk')->nullable();
            $table->integer('merangkak')->nullable();
            $table->integer('berdiri')->nullable();
            $table->integer('berjalan')->nullable();
            $table->boolean('masalah_neonatus')->nullable();
            $table->string('masalah_neonatus_text')->nullable();
            $table->boolean('kelainan_kongenita')->nullable();
            $table->string('kelainan_kongenita_text')->nullable();
            $table->string('keluhan_tumbuh_tembang')->nullable();
            $table->boolean('risiko_tinggi')->nullable();
            $table->string('riwayat_penyakit_sekarang')->nullable();
            $table->string('riwayat_penyakit_dahulu')->nullable();
            $table->string('riwayat_penyakit_keluarga')->nullable();
            $table->string('tindakan_resusitasi')->nullable();
            $table->integer('gds')->nullable();
            $table->string('kepala')->nullable();
            $table->string('mata')->nullable();
            $table->string('mulut')->nullable();
            $table->string('leher')->nullable();
            $table->string('thoraks_cor')->nullable();
            $table->string('thoraks_pulmo')->nullable();
            $table->string('abdomen')->nullable();
            $table->string('extremitas')->nullable();
            $table->string('anus_genitalia')->nullable();
            $table->string('lain_lain')->nullable();
            $table->bigInteger('laboratorium_id')->nullable();
            $table->string('ekg')->nullable();
            $table->string('xray')->nullable();
            $table->string('diagnosis_kerja')->nullable();
            $table->string('diagnosis_banding')->nullable();
            $table->string('diagnosis_keperawatan')->nullable();
            $table->string('rencana_terapi')->nullable();
            $table->text('rencana_tindak_lanjut')->nullable();
            $table->string('edukasi_pasien')->nullable();
            $table->string('riwayat_pengobatan')->nullable();
            $table->text('neurologis_kepala')->nullable();
            $table->text('neurologis_leher')->nullable();
            $table->string('neurologis_vertebra')->nullable();
            $table->text('neurologis_extrimitas')->nullable();
            $table->string('elektromedik')->nullable();
            $table->string('tindakan')->nullable();
            $table->string('medika_mentosa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_assesments');
    }
};
