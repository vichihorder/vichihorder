<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUserTransaction extends Migration
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

        DB::statement("ALTER TABLE user_transaction CHANGE COLUMN transaction_type transaction_type ENUM('DEPOSIT','WITHDRAWAL','ORDER_REFUND','ORDER_DEPOSIT','ORDER_PAYMENT','ADJUSTMENT','CHARGE_FEE','GIFT','PAYMENT','PROMOTION','REFUND_COMPLAINT','DEPOSIT_ADJUSTMENT')");
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
