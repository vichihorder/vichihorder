<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComplaints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->text('description')->nullable();
            $table->integer('accept_by');
            $table->integer('reject_by');
            $table->string('status',50)->nullable();
            $table->text('title')->nullable();
            $table->dateTime('accept_time')->nullable();
            $table->dateTime('reject_time')->nullable();
            $table->dateTime('created_time')->nullable();
            $table->dateTime('finish_time')->nullable();
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
        Schema::dropIfExists('complaints');
    }
}
