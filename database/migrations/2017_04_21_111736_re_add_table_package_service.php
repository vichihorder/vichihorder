<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReAddTablePackageService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_service', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->nullable();
            $table->string('logistic_package_barcode', 20)->nullable();
            $table->integer('order_id')->nullable();
            $table->string('order_code', 20)->nullable();
            $table->string('code', 20)->nullable();
            $table->enum('status', ['ACTIVE', 'DISABLED'])->default('ACTIVE');
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
        Schema::dropIfExists('package_service');
    }
}
