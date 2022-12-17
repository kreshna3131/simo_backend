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
        Schema::create('icd_ten_fills', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->nullable();
            $table->string('parent_kode')->nullable();
            $table->string('nama')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('aktif')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamp('created_date')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_date')->nullable();
            $table->integer('revised')->default(1);
            $table->integer('m_unit_id')->nullable();
            $table->string('kode')->nullable();
            $table->string('alias')->nullable();
            $table->string('dtd_kode')->nullable();
            $table->string('dtd_nama')->nullable();
            $table->string('unit_id')->default(0);
            $table->integer('dtd_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('icd_ten_fills');
    }
};
