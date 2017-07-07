<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDeliveries2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('create_user')->nullable()->commment('nguoi tao phieu');
            $table->char('code', 100)->nullable()->unique()->comment('ma phieu');
            $table->double('domestic_shipping_vietnam', 20, 2)->comment('tien van chuyen noi dia');
            $table->double('amount_cod', 20, 2)->comment('tien thu ho');
            $table->string('packages', 255)->nullable()->comment('danh sach kien hang tren phieu, cach nhau boi day phay');
            $table->string('orders', 255)->nullable()->comment('danh sach don hang tren phieu, cach nhau boi dau phay');
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
        Schema::dropIfExists('deliveries');
    }
}
