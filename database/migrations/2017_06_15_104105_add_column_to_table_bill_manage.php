<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToTableBillManage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_manage', function (Blueprint $table) {
            $table->integer('buyer_id')->nullable()->comment('id khach')->after('orders');
            $table->integer('buyer_address_id')->nullable()->comment('id dia chi nhan hang cua khach')->after('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_manage', function (Blueprint $table) {
            //
        });
    }
}
