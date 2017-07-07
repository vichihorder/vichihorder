<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTableServiceShipping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_shipping', function (Blueprint $table) {
            //
        });

        DB::statement("
          update service_shipping set weight_fee_first = 0 where weight_fee_first > 0
        ");

        DB::statement("
            update service_shipping set weight_fee = 19000 where id = 2
        ");

        DB::statement("
            update service_shipping set weight_fee = 29000 where id = 7
        ");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_shipping', function (Blueprint $table) {
            //
        });
    }
}
