<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableServiceShipping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_shipping', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 50)->nullable();
            $table->string('type', 20);
            $table->string('sub_type', 100);
            $table->decimal('weight_from', 20, 1);
            $table->decimal('weight_to', 20, 1);
            $table->double('weight_fee', 20, 2);
            $table->double('weight_fee_first', 20, 2)->default(0)->comment('So tien can nang dau tien tinh phi');
            $table->enum('status', ['ACTIVE', 'DISABLED'])->default('ACTIVE');
            $table->timestamp('actived_time')->nullable();
            $table->timestamp('deadline_time')->nullable();
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
        Schema::dropIfExists('service_shipping');
    }
}
