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
        Schema::create('integration_results', function (Blueprint $table) {
            $table->id();
            $table->string('visit_id')->nullable();
            $table->string('no_rm')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('sub_assesment_id')->nullable();
            $table->string('integration')->nullable();
            $table->string('keluhan')->nullable();
            $table->string('keadaan_umum')->nullable();
            $table->string('tindakan_resusitasi')->nullable();
            $table->string('tekanan_darah')->nullable();
            $table->string('frekuensi_nadi')->nullable();
            $table->string('frekuensi_napas')->nullable();
            $table->decimal('berat_badan')->nullable();
            $table->integer('tinggi_badan')->nullable();
            $table->integer('suhu_badan')->nullable();
            $table->integer('gds')->nullable();
            $table->string('diagnosis_kerja')->nullable();
            $table->string('diagnosis_keperawatan')->nullable();
            $table->string('rencana_terapi')->nullable();
            $table->text('rencana_tindak_lanjut')->nullable();
            $table->text('implementasi')->nullable();
            $table->string('info')->nullable();
            $table->string('assesment_name')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('integration_results');
    }
};
