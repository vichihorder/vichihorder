<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserPaidSaleSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_paid_sale_setting', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('paid_user_id')->nullable()->comment('id nhan vien mua hang');
            $table->dateTime('activated_at')->nullable()->comment('thoi gian bat dau nhan luong');
            $table->dateTime('deadlined_at')->nullable()->comment('thoi gian ket thuc nhan luong');
            $table->double('salary_basic', 20, 2)->comment('luong co ban');
            $table->double('rose_percent', 20, 2)->comment('phan tram hoa hong tien mac ca');
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
        Schema::dropIfExists('user_paid_sale_setting');
    }
}
