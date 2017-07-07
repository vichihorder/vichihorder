<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSendEmailCustomerQueue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('send_email_customer_queue', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->nullable()
                ->comment('gửi cho nhiều trường hợp nên có thể null'); // có thể có giá trị null
            $table->string('email');
            $table->integer('user_id');
            $table->text('content');
            $table->string('send_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('send_email_customer_queue');
    }
}
