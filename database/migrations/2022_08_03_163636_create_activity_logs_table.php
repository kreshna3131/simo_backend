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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->string('unique_id')->nullable();
            $table->string('visit_id')->nullable();
            $table->bigInteger('request_id')->nullable();
            $table->bigInteger('soap_id')->nullable();
            $table->bigInteger('dokumen_id')->nullable();
            $table->bigInteger('sub_dokumen_id')->nullable();
            $table->string('note')->nullable();
            $table->string('type')->nullable();
            $table->string('action')->nullable();
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
        Schema::dropIfExists('activity_logs');
    }
};
