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
        Schema::create('concoction_medicines', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('concoction_id')->nullable();
            $table->string('medicine_id')->nullable();
            $table->string('name')->nullable();
            $table->string('unit')->nullable();
            $table->string('dose')->nullable();
            $table->string('strength')->nullable();
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
        Schema::dropIfExists('concoction_medicines');
    }
};
