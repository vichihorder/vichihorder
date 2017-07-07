<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cart_id')->nullable()->default(0);
            $table->integer('user_id')->nullable()->default(0);
            $table->string('title_origin', 100)->nullable()->comment('tieu de san pham bang tieng trung');
            $table->string('title_translated', 100)->nullable();
            $table->double('price_origin', 20, 2)->nullable()->default(0)->commment('gia goc');
            $table->double('price_promotion', 20, 2)->nullable()->default(0)->comment('gia khuyen mai');
            $table->string('price_table', 100)->nullable()->comment('khoang gia tren 1688');
            $table->string('data_value', 100)->nullable()->comment('cap thuoc tinh san pham khach da chon');
            $table->string('property', 50)->nullable()->comment('thuoc tinh san pham');
            $table->string('property_translated', 50)->nullable();
            $table->string('property_md5', 50)->nullable();
            $table->mediumText('image_origin', 100)->nullable()->comment('anh goc san pham');
            $table->mediumText('image_model', 100)->nullable()->comment('anh bien the khach chon');
            $table->string('seller_id', 50)->nullable()->comment('id nguoi ban');
            $table->string('shop_id', 50)->nullable()->comment('id shop');
            $table->string('shop_name', 50)->nullable()->comment('ten shop');
            $table->string('wangwang', 50)->nullable()->comment('nick chat cua shop');
            $table->integer('quantity')->nullable()->default(0);
            $table->integer('require_min')->nullable()->default(0);
            $table->integer('stock')->nullable()->default(0);
            $table->string('location_sale', 100)->nullable();
            $table->string('site', 10)->nullable();
            $table->string('item_id', 30)->nullable();
            $table->string('link_origin', 100)->nullable()->comment('link san pham goc tren trang trung quoc');
            $table->string('outer_id', 50)->nullable();
            $table->double('weight', 20, 2)->nullable()->default(0);
            $table->integer('error')->nullable();
            $table->integer('step')->nullable()->default(0)->comment('boi so cua san pham khi mua');
            $table->string('tool', 50)->nullable();
            $table->string('version', 50)->nullable();
            $table->integer('is_translate')->nullable()->comment('cho biet khi dat hang co dich hay khong?');
            $table->string('comment', 100)->nullable();
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
        Schema::dropIfExists('cart_items');
    }
}
