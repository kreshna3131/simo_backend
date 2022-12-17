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
        Schema::create('icd_tens', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('visit_id')->nullable();
            $table->string('no_rm')->nullable();
            $table->string('kode')->nullable();
            $table->string('name')->nullable();
            $table->string('diagnosis_type')->nullable();
            $table->string('case')->nullable();
            $table->string('status')->nullable();
            $table->string('created_by')->nullable();
            $table->string('created_role')->nullable();
            $table->string('visit_number')->nullable();
            $table->boolean('is_add')->default(1);
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
        Schema::dropIfExists('icd_tens');
    }
};
