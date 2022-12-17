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
        Schema::create('concoctions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('recipe_id')->nullable();
            $table->string('name')->nullable();
            $table->integer('total')->nullable();
            $table->string('use_time')->nullable();
            $table->string('suggestion_use')->nullable();
            $table->string('medicine_count')->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('concoctions');
    }
};
