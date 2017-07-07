<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStatusToTableComplaints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->integer('doing_by')->nullable()->after('accept_by')->comment('ai la nguoi dang lam');
            $table->dateTime('doing_time')->nullable()->after('accept_time')->comment('ai la nguoi dang lam');
            $table->integer('finish_by')->nullable()->after('doing_by')->comment('hoan thanh boi ai');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('complaints', function (Blueprint $table) {
            //
        });
    }
}
