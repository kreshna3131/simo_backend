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
        Schema::create('icd_nine_fills', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->nullable();
            $table->string('nama')->nullable();
            $table->string('alias')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('aktif')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamp('created_date')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('updated_date')->nullable();
            $table->integer('revised')->default(0);
            $table->integer('reg_company_id')->default(1);
            $table->integer('reg_apps_id')->default(1);
            $table->string('unit_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('icd_nine_fills');
    }
};
