<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToTableUserPaidSaleSetting1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_paid_sale_setting', function (Blueprint $table) {
            $table->double('rose_percent_min', 20, 2)->after('rose_percent')->comment('hoa hong toi thieu');
            $table->double('require_min_bargain_percent', 20, 2)->after('rose_percent')->comment('chi tieu mac ca toi thieu');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_paid_sale_setting', function (Blueprint $table) {
            //
        });
    }
}
