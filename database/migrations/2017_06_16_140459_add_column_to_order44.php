<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToOrder44 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order', function (Blueprint $table) {
            $table->double('amount_original', 20, 2)
                ->after('account_purchase_origin')
                ->comment('Tien hang goc o site trung quoc');
            $table->double('domestic_shipping_china_original', 20, 2)
                ->after('account_purchase_origin')
                ->comment('Tien van chuyen noi dia o site trung quoc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order', function (Blueprint $table) {
            //
        });
    }
}
