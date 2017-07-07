<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToUsers9 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->double('sale_basic', 20, 2)
                ->comment('Luong co ban cua nhan vien mua hang')
                ->after('order_deposit_percent');
            $table->double('sale_percent', 20, 2)
                ->comment('phan tram hoa hong nhan duoc dua tren so tien mac ca')
                ->after('order_deposit_percent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
