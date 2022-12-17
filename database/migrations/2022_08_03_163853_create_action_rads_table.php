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
        Schema::create('action_rads', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('request_rad_id')->nullable();
            $table->bigInteger('action_id')->nullable();
            $table->bigInteger('action_group_id')->nullable();
            $table->string('action_group')->nullable();
            $table->string('name')->nullable();
            $table->text('result')->nullable();
            $table->string('status')->nullable();
            $table->integer('order_number')->nullable();
            $table->integer('attachment_count')->nullable();
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
        Schema::dropIfExists('action_rads');
    }
};
