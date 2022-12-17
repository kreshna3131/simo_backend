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
        Schema::create('request_rehabs', function (Blueprint $table) {
            $table->id();
            $table->string('visit_id')->nullable();
            $table->string('no_rm')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('unique_id')->nullable();
            $table->string('info')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_read_rehab')->default(1);
            $table->boolean('is_read_doc')->default(1);
            $table->string('created_by')->nullable();
            $table->string('created_for')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('visit_number')->nullable();
            $table->date('done_at')->nullable();
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
        Schema::dropIfExists('request_rehabs');
    }
};
