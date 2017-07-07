<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToOrder99 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order', function (Blueprint $table) {
            /* Tra lai tren don hang */
            $table->double('order_refund', 20, 2)->default(0)->after('payment')
                ->comment('Tong tien tra lai tren don hang - ndt');
            $table->double('order_refund_vnd', 20, 2)->default(0)->after('payment')
                ->comment('Tong tien tra lai tren don hang - vnd');


            /* Tra lai tu KNDV */
            $table->double('complaint_refund', 20, 2)->default(0)->after('payment')
                ->comment('Tong tien tra lai tu khieu nai tren don hang - ndt');
            $table->double('complaint_refund_vnd', 20, 2)->default(0)->after('payment')
                ->comment('Tong tien tra lai tu khieu nai tren don hang - vnd');

            /* Tong thanh toan */

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
