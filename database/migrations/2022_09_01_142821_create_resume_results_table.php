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
        Schema::create('resume_results', function (Blueprint $table) {
            $table->id();
            $table->string('visit_id')->nullable();
            $table->string('no_rm')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('sub_assesment_id')->nullable();
            $table->string('keluhan')->nullable();
            $table->string('riwayat_penyakit_sekarang')->nullable();
            $table->string('riwayat_penyakit_dahulu')->nullable();
            $table->string('riwayat_penyakit_keluarga')->nullable();
            $table->string('keadaan_umum')->nullable();
            $table->string('tindakan_resusitasi')->nullable();
            $table->string('tekanan_darah')->nullable();
            $table->string('frekuensi_nadi')->nullable();
            $table->string('frekuensi_napas')->nullable();
            $table->decimal('berat_badan')->nullable();
            $table->integer('tinggi_badan')->nullable();
            $table->integer('suhu_badan')->nullable();
            $table->integer('gds')->nullable();
            $table->bigInteger('laboratorium_id')->nullable();
            $table->string('ekg')->nullable();
            $table->string('xray')->nullable();
            $table->string('diagnosis_banding')->nullable();
            $table->string('diagnosis_kerja')->nullable();
            $table->string('tindakan')->nullable();
            $table->string('info')->nullable();
            $table->string('assesment_name')->nullable();
            $table->string('created_by')->nullable();
            $table->string('created_role')->nullable();
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
        Schema::dropIfExists('resume_results');
    }
};
