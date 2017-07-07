<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertDataTableServiceBuying extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_buying', function (Blueprint $table) {
            //
        });

        DB::statement("truncate table service_buying");
        DB::statement("ALTER TABLE service_buying AUTO_INCREMENT = 1");

        DB::statement("
        
        INSERT INTO `service_buying` (`id`, `fee_percent`, `min_fee`, `begin`, `end`, `status`, `actived_time`, `deadline_time`, `created_at`, `updated_at`)
VALUES
	(1, 5.0, 0.00, 0.00, 10000000.00, 'ACTIVE', '2016-01-01 00:00:00', '2017-04-25 23:59:59', '2017-04-26 00:00:00', NULL),
	(2, 4.0, 0.00, 10000001.00, 50000000.00, 'ACTIVE', '2016-01-01 00:00:00', '2017-04-25 23:59:59', '2017-04-26 00:00:00', NULL),
	(3, 3.0, 0.00, 50000001.00, 70000000.00, 'ACTIVE', '2016-01-01 00:00:00', '2017-04-25 23:59:59', '2017-04-26 00:00:00', NULL),
	(4, 2.0, 0.00, 70000001.00, 100000000.00, 'ACTIVE', '2016-01-01 00:00:00', '2017-04-25 23:59:59', '2017-04-26 00:00:00', NULL),
	(5, 1.0, 0.00, 100000001.00, 1000000000000000000.00, 'ACTIVE', '2016-01-01 00:00:00', '2017-04-25 23:59:59', '2017-04-26 00:00:00', NULL),
	(6, 1.0, 0.00, 0.00, 1000000000000000000.00, 'ACTIVE', '2017-04-26 00:00:00', '2029-04-25 23:59:59', '2017-04-26 00:00:00', NULL)

        
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_buying', function (Blueprint $table) {
            //
        });
    }
}
