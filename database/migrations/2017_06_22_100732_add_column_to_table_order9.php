<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToTableOrder9 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order', function (Blueprint $table) {
            $table->double('amount_original_vnd', 20, 2)
                ->after('amount_original')
                ->comment('tong gia thuc mua vnd');

            $table->double('customer_amount', 20, 2)
                ->after('amount_vnd')
                ->comment('tong gia bao khach (tien hang + ship noi dia TQ)');

            $table->double('customer_amount_vnd', 20, 2)
                ->after('amount_vnd')
                ->comment('tong gia bao khach (tien hang + ship noi dia TQ) vnd');

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
