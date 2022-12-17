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
        Schema::create('comment_labs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('request_lab_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('message')->nullable();
            $table->boolean('is_read_lab')->nullable();
            $table->boolean('is_read_doc')->nullable();
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
        Schema::dropIfExists('comment_labs');
    }
};
