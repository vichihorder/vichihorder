<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUserTransaction2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_transaction', function (Blueprint $table) {
            //
        });

        DB::statement(" update user_transaction set transaction_type = 'ORDER_REFUND' where transaction_type = '' ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_transaction', function (Blueprint $table) {
            //
        });
    }
}
