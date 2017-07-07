<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertDataTableServiceShipping extends Migration
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

        DB::statement("truncate table service_shipping");
        DB::statement("ALTER TABLE service_shipping AUTO_INCREMENT = 1");

        DB::statement("
        
        INSERT INTO `service_shipping` (`id`, `title`, `type`, `sub_type`, `weight_from`, `weight_to`, `weight_fee`, `weight_fee_first`, `status`, `actived_time`, `deadline_time`, `created_at`, `updated_at`)
VALUES
	(1, 'Vận chuyển nhanh HN 0-10kg', 'CHINA_VIETNAM', '01', 0.0, 10.0, 22000.00, 100000.00, 'ACTIVE', '2017-04-26 00:00:00', '2029-04-25 00:00:00', '2017-04-26 00:00:00', NULL),
	(2, 'Vận chuyển nhanh HN 10.1-50kg', 'CHINA_VIETNAM', '01', 10.1, 50.0, 18000.00, 100000.00, 'ACTIVE', '2017-04-26 00:00:00', '2029-04-25 00:00:00', '2017-04-26 00:00:00', NULL),
	(3, 'Vận chuyển nhanh HN 50.1-200kg', 'CHINA_VIETNAM', '01', 50.1, 200.0, 18000.00, 0.00, 'ACTIVE', '2017-04-26 00:00:00', '2029-04-25 00:00:00', '2017-04-26 00:00:00', NULL),
	(4, 'Vận chuyển nhanh HN 200.1-1000kg', 'CHINA_VIETNAM', '01', 200.1, 1000.0, 16000.00, 0.00, 'ACTIVE', '2017-04-26 00:00:00', '2029-04-25 00:00:00', '2017-04-26 00:00:00', NULL),
	(5, 'Vận chuyển nhanh HN >= 1000.1kg', 'CHINA_VIETNAM', '01', 1000.1, 1000000.0, 14000.00, 0.00, 'ACTIVE', '2017-04-26 00:00:00', '2029-04-25 00:00:00', '2017-04-26 00:00:00', NULL),
	(6, 'Vận chuyển nhanh SG 0-10kg', 'CHINA_VIETNAM', '02', 0.0, 10.0, 32000.00, 100000.00, 'ACTIVE', '2017-04-26 00:00:00', '2029-04-25 00:00:00', '2017-04-26 00:00:00', NULL),
	(7, 'Vận chuyển nhanh SG 10.1-50kg', 'CHINA_VIETNAM', '02', 10.1, 50.0, 28000.00, 100000.00, 'ACTIVE', '2017-04-26 00:00:00', '2029-04-25 00:00:00', '2017-04-26 00:00:00', NULL),
	(8, 'Vận chuyển nhanh SG 50.1-200kg', 'CHINA_VIETNAM', '02', 50.1, 200.0, 28000.00, 0.00, 'ACTIVE', '2017-04-26 00:00:00', '2029-04-25 00:00:00', '2017-04-26 00:00:00', NULL),
	(9, 'Vận chuyển nhanh SG 200.1-1000kg', 'CHINA_VIETNAM', '02', 200.1, 1000.0, 26000.00, 0.00, 'ACTIVE', '2017-04-26 00:00:00', '2029-04-25 00:00:00', '2017-04-26 00:00:00', NULL),
	(10, 'Vận chuyển nhanh SG >= 1000.1kg', 'CHINA_VIETNAM', '02', 1000.1, 1000000.0, 24000.00, 0.00, 'ACTIVE', '2017-04-26 00:00:00', '2029-04-25 00:00:00', '2017-04-26 00:00:00', NULL),
	(11, 'Vận chuyển nhanh HN 0-10kg', 'CHINA_VIETNAM', '01', 0.0, 10.0, 27000.00, 0.00, 'ACTIVE', '2016-01-01 00:00:00', '2017-04-25 23:59:59', '2017-04-26 00:00:00', NULL),
	(12, 'Vận chuyển nhanh HN 10.1-20kg', 'CHINA_VIETNAM', '01', 10.1, 20.0, 25000.00, 0.00, 'ACTIVE', '2016-01-01 00:00:00', '2017-04-25 23:59:59', '2017-04-26 00:00:00', NULL),
	(13, 'Vận chuyển nhanh HN 20.1-70kg', 'CHINA_VIETNAM', '01', 20.1, 70.0, 23000.00, 0.00, 'ACTIVE', '2016-01-01 00:00:00', '2017-04-25 23:59:59', '2017-04-26 00:00:00', NULL),
	(14, 'Vận chuyển nhanh HN 70.1-200kg', 'CHINA_VIETNAM', '01', 70.1, 200.0, 22000.00, 0.00, 'ACTIVE', '2016-01-01 00:00:00', '2017-04-25 23:59:59', '2017-04-26 00:00:00', NULL),
	(16, 'Vận chuyển nhanh SG 0-10kg', 'CHINA_VIETNAM', '02', 0.0, 10.0, 37000.00, 0.00, 'ACTIVE', '2016-01-01 00:00:00', '2017-04-25 23:59:59', '2017-04-26 00:00:00', NULL),
	(17, 'Vận chuyển nhanh SG 10.1-20kg', 'CHINA_VIETNAM', '02', 10.1, 20.0, 35000.00, 0.00, 'ACTIVE', '2016-01-01 00:00:00', '2017-04-25 23:59:59', '2017-04-26 00:00:00', NULL),
	(18, 'Vận chuyển nhanh SG 20.1-70kg', 'CHINA_VIETNAM', '02', 20.1, 70.0, 33000.00, 0.00, 'ACTIVE', '2016-01-01 00:00:00', '2017-04-25 23:59:59', '2017-04-26 00:00:00', NULL),
	(19, 'Vận chuyển nhanh SG 70.1-200kg', 'CHINA_VIETNAM', '02', 70.1, 200.0, 32000.00, 0.00, 'ACTIVE', '2016-01-01 00:00:00', '2017-04-25 23:59:59', '2017-04-26 00:00:00', NULL)

        
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
