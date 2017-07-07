<?php

namespace App\Console\Commands;

use App\Library\Sms\SendInfoOrderToWarehouse;
use App\SendSmsToCustomer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;


class SendSmsInfoOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_info_order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'hàm gửi tin nhắn cho khách khi hàng về đúng kho';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $smsSend = SendSmsToCustomer::where('send_status','NOT_YET')->paginate(20);

        if(count($smsSend) > 0){

            $sms = new SendInfoOrderToWarehouse();
            foreach ($smsSend as $item_sms){

                $result = $sms->sendSms([$item_sms->phone],$item_sms->content);

                if($result['status'] == 'success'){
                    SendSmsToCustomer::where('id', $item_sms->id)
                        ->update(['send_status' => SendSmsToCustomer::SUCCESS]);
                }elseif ($result['status'] == 'error'){
                    SendSmsToCustomer::where('id', $item_sms->id)
                        ->update(['send_status' => SendSmsToCustomer::FAIL]);
                }

                Log::info('gui-tin-nhan',[$result]);

            }

        }

    }
}
