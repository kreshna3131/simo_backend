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
        Schema::create('action_labs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('request_lab_id')->nullable();
            $table->bigInteger('action_id')->nullable();
            $table->bigInteger('action_group_id')->nullable();
            $table->string('action_group')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->integer('order_number')->nullable();
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
        Schema::dropIfExists('action_labs');
    }
};
