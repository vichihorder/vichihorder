<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->default(0)->comment('id khach dat don hang');
            $table->string('seller_id', 255)->nullable()->comment('id nguoi ban hang');
            $table->string('wangwang', 255)->nullable()->comment('nick wang wang cua trung quoc');
            $table->string('location_sale', 255)->nullable()->comment('dia chi shop trung quoc');
            $table->string('shop_id', 50)->nullable()->comment('id shop trung quoc');
            $table->string('shop_name', 50)->nullable()->comment('ten shop trung quoc');
            $table->string('shop_link', 100)->nullable()->comment('url shop detail trung quoc');
            $table->string('avatar', 100)->nullable()->comment('anh dau tien cua san pham');
            $table->string('site', 50)->nullable()->comment('site taobao, tmall hoac 1688');
            $table->string('services', 100)->nullable('dich vu khach chon tren gio hang');
            $table->string('comment', 255)->nullable();
            $table->string('comment_private', 255)->nullable();
            $table->timestamp('last_insert_item_at')->nullable()->before('created_at');
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
        Schema::dropIfExists('carts');
    }
}
