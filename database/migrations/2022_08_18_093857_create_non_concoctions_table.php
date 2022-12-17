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
        Schema::create('non_concoctions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('recipe_id')->nullable();
            $table->string('medicine_id')->nullable();
            $table->string('medicine_name')->nullable();
            $table->string('medicine_unit')->nullable();
            $table->string('medicine_use_time')->nullable();
            $table->string('medicine_suggestion_use')->nullable();
            $table->integer('medicine_quantity')->nullable();
            $table->string('medicine_note')->nullable();
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
        Schema::dropIfExists('non_concoctions');
    }
};
