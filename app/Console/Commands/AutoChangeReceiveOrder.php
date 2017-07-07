<?php

namespace App\Console\Commands;

use App\Comment;
use App\Order;
use App\SystemConfig;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoChangeReceiveOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto_change_receive_order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nhung don hang o trang thai dang giao hang, sau 3 ngay neu khach khong an da nhan thi he thong se tu dong chuyen sang trang thai da nhan hang';

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
        $day_auto_change_order_receive = SystemConfig::getConfigValueByKey('day_auto_change_order_receive');
        if(!$day_auto_change_order_receive){
            Log::info("Chua co cau hinh ngay tu dong chuyen trang thai don sang da nhan");
            exit;
        }

        $day = $day_auto_change_order_receive;
        $l_time = strtotime("-{$day} day", strtotime(date('Y-m-d H:i:s')));
        $l_time = date('Y-m-d H:i:s', $l_time);

        $orders_not_receive_long_time_3_day = DB::select("
        
        select id, delivering_at from `order` 
        where `status` = '".Order::STATUS_DELIVERING."' 
        and `delivering_at` <= '".$l_time."' limit 100;
        ");

        if($orders_not_receive_long_time_3_day){
            foreach($orders_not_receive_long_time_3_day as $orders_not_receive_long_time_3_day_item){
                $order_id = $orders_not_receive_long_time_3_day_item->id;
                $order = Order::find($order_id);
                if(!$order instanceof Order){
                    continue;
                }

                $order->changeStatus(Order::STATUS_RECEIVED, false);

                if($order->save()){
                    $message = sprintf("Đơn đang giao hàng, sau %s ngày hệ thống tự động chuyển sang Đã Nhận", $day);
                    Comment::createComment(null, $order,
                        $message,
                        Comment::TYPE_EXTERNAL,
                        Comment::TYPE_CONTEXT_LOG);

                    Comment::createComment(null, $order,
                        $message,
                        Comment::TYPE_INTERNAL,
                        Comment::TYPE_CONTEXT_LOG);
                    Log::info(sprintf("He thong tu dong chuyen don hang %s sang trang thai Da Nhan", $order->code));
                }else{
                    Log::warn(sprintf("Chuyen trang thai don hang %s sang Da Nhan khong thanh cong", $order->code));
                }

            }
        }
    }
}
