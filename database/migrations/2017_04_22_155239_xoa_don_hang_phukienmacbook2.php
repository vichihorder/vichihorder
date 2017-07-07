<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class XoaDonHangPhukienmacbook2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order', function (Blueprint $table) {
            //
        });

        $user_id = 23;

        DB::statement(" delete from user_transaction where user_id = $user_id ");
        DB::statement(" delete from `order` where user_id = $user_id ");
        DB::statement(" delete from `packages` where buyer_id = $user_id ");
        DB::statement(" update users set account_balance = 0 where id = $user_id ");
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
