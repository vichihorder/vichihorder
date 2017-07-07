<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableOrderFee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_fee', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->nullable();
            $table->double('money', 20, 2)->nullable()->default(0);
            $table->integer('order_id')->nullable()->default(0);
            $table->string('order_code', 50)->nullable();
            $table->integer('user_id')->nullable()->default(0);
            $table->string('user_code')->nullable();
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
        Schema::dropIfExists('order_fee');
    }
}
