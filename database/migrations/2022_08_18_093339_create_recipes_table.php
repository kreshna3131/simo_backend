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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('visit_id')->nullable();
            $table->string('no_rm')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('nota_number')->nullable();
            $table->string('unique_id')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('info')->nullable();
            $table->boolean('is_read_apo')->default(1);
            $table->boolean('is_read_doc')->default(1);
            $table->string('created_by')->nullable();
            $table->string('created_for')->nullable();
            $table->string('visit_number')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('recipes');
    }
};
