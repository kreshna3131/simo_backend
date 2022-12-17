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
        Schema::create('attribute_template', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('template_id');
            $table->bigInteger('attribute_id');
            $table->bigInteger('group_id');
            $table->string('group_name');
            $table->string('rules');
            $table->boolean('status');
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
        Schema::dropIfExists('template_attribute');
    }
};
