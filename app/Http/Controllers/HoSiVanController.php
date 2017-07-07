<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Exchange;
use App\Library\ServiceFee\ServiceFactoryMethod;
use App\Order;
use App\OrderFee;
use App\Package;
use App\Service;
use App\SystemConfig;
use App\User;
use App\UserAddress;
use App\UserTransaction;
use App\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HoSiVanController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    private function __order_buying_statistic_excel(Request $request){
        $start_time = $request->get('start_time');
        $end_time = $request->get('end_time');

        if(empty($start_time) || empty($end_time)){
            die('chua co du lieu dau vao');
        }

        if(!Util::validateDate($start_time, 'Y-m-d')){
            die('ngay bat dau khong hop le');
        }

        if(!Util::validateDate($end_time, 'Y-m-d')){
            die('ngay bat dau khong hop le');
        }

        $csv = "Ngày mua hàng\tNhân viên mua hàng\tMã đơn\tTỉ giá\tTiền hàng(1)\tMua Hàng(2)\tVC nội địa TQ (3)\tVC quốc tế (4)\tĐóng gỗ (5)\tPhí đơn hàng (6=1+2+3+4+5)\r\n";

        $current_time = $start_time;
        while(strtotime($current_time) <= strtotime($end_time)){

            $orders_today = $this->__get_order_buying_today($current_time);

            foreach($orders_today as $key => $orders_today_item){
                $user_buying = User::find($key);
                if(!$user_buying instanceof User){
                    continue;
                }

                if(count($orders_today_item)){
                    foreach($orders_today_item as $o){
                        $o_fee = $o->fee();

                        $order_list = array(
                            $current_time,
                            sprintf("%s - %s - %s",
                                $user_buying->name,
                                $user_buying->code,
                                $user_buying->email),
                            $o->code,
                            $o->exchange_rate,
                            $o_fee['AMOUNT_VND'],
                            $o_fee['BUYING_FEE_VND'],
                            $o_fee['DOMESTIC_SHIPPING_FEE_VND'],
                            $o_fee['SHIPPING_CHINA_VIETNAM_FEE_VND'],
                            $o_fee['WOOD_CRATING_VND'],
                            $o_fee['TOTAL_FEE_VND']
                        );

                        $csv .= join("\t", $order_list)."\r\n";

                    }
                }
            }

            $current_time = date('Y-m-d', strtotime("+1 day", strtotime($current_time)));
        }

        $file_name = sprintf("thong-ke-tu-%s-den-%s.csv", $start_time, $end_time);

        $csv = chr(255).chr(254).mb_convert_encoding($csv, "UTF-16LE", "UTF-8");
        header("Content-type: application/x-msdownload");
        header("Content-disposition: csv; filename=" . $file_name . "; size=".strlen($csv));
        echo $csv;

        die();
    }

    private function __get_order_buying_today($today){

        $orders = Order::where([
            ['bought_at', '>=', sprintf("%s 00:00:00", $today)],
            ['bought_at', '<=', sprintf("%s 23:59:59", $today)]
        ])->get();

        $return = [];
        if($orders){
            foreach($orders as $order){
                $return[sprintf("%s", $order->paid_staff_id)][] = $order;
            }
        }
        return $return;
    }

    private function __phpinfo(){
        die(phpinfo());
    }

    private function __cap_nhat_lai_thoi_gian_nhan_hang_tren_don(Request $request){
        $day_auto_change_order_receive = SystemConfig::getConfigValueByKey('day_auto_change_order_receive');

        $orders = DB::select(" select * from `order` where `status` = 'RECEIVED'; ");
        if($orders){
            foreach($orders as $o){
                $order = Order::find($o->id);
                if(!$order instanceof Order){
                    continue;
                }
                $delivering_at = $order->delivering_at;
                if(Util::validateDate($delivering_at, 'Y-m-d H:i:s')){

                    if($day_auto_change_order_receive){
                        $received_at = strtotime(date("Y-m-d H:i:s", strtotime($delivering_at)) . " +{$day_auto_change_order_receive} day");
                        $order->received_at = date('Y-m-d H:i:s', $received_at);
                        $order->save();
                    }
                }
            }
        }
    }

    /**
     * @author vanhs
     * @desc
     * cap nhat them cac thong tin
     *
     * - tong gia thuc mua vnd
     * - tien hang vnd
     * - tong gia bao khach
     * - tong gia bao khach vnd
     * - tien mac ca
     * - tien mac ca vnd
     *
     * @param Request $request
     */
    private function __cap_nhat_thong_tin_don_hang1(Request $request){
        $orders = DB::select(" select id from `order` where flag is null limit 250; ");

        if($orders){
            foreach($orders as $o){
                $order = Order::find($o->id);
                if(!$order instanceof Order){
                    continue;
                }
                $order->flag = 1;
                $order->save();
            }
        }
    }

    private function __hoan_tien_van_chuyen_don_van_chuyen_tiet_kiem(Request $request){

        $orders_id = $request->get('orders_id');//co dang (100, 200, 300)
        if(empty($orders_id)){
            die('thieu tham so truyen vao');
        }

        $orders_list = explode(',', $orders_id);
        foreach($orders_list as $orders_list_item){
            $order = Order::findOneByIdOrCode($orders_list_item);
            if(!$order instanceof Order){
                continue;
            }



            $fee_shipping_fast = 0;
            $fee_shipping_slow = 0;
            $fee_refund = $fee_shipping_fast > $fee_shipping_slow
                ? $fee_shipping_fast - $fee_shipping_slow : 0;

            echo "<hr>";
            echo sprintf("<h3>Don %s</h3> <p>Tien van chuyen nhanh %s đ</p> <p>Tien van chuyen cham %s đ</p>

                <p>Tien can tra lai la %s đ</p>
            ",

                $order->code,
                Util::formatNumber($fee_shipping_fast),
                Util::formatNumber($fee_shipping_slow),
                Util::formatNumber($fee_refund)
                );
            echo "<hr>";
        }
    }

    private function __don_hang_lech_tai_chinh(Request $request){

        die('xong');
        $orders_has_change_deposit_amount = UserTransaction::where([
            ['transaction_type', '=', UserTransaction::TRANSACTION_TYPE_DEPOSIT_ADJUSTMENT],
            ['state', '=', UserTransaction::STATE_COMPLETED],
            ['object_type', '=', UserTransaction::OBJECT_TYPE_ORDER]
        ])->get();

        if($orders_has_change_deposit_amount){
            foreach($orders_has_change_deposit_amount as $orders_has_change_deposit_amount_row){
                if(!$orders_has_change_deposit_amount_row instanceof UserTransaction){
                    continue;
                }
                $order = Order::find($orders_has_change_deposit_amount_row->object_id);
                if(!$order instanceof Order){
                    continue;
                }

                if(!$order->isAfterStatus(Order::STATUS_TRANSPORTING, true)){
                    continue;
                }

                if($order->status == Order::STATUS_CANCELLED){
                    continue;
                }

                $customer = User::find($order->user_id);

                /* so tien khach hang da thanh toan */
                $customer_payment_amount_vnd = UserTransaction::getCustomerPaymentWithOrder($order->id);
                $need_payment_amount = $order->getFeeAll();
                if($need_payment_amount > $customer_payment_amount_vnd){
                    echo sprintf("<p>don hang <a href='%s'>%s</a> 
- %s (%s)
- can thanh toan %s 
- da thanh toan %s</p>",
                        url('order/detail', $order->id),
                        $order->code,
                        $customer->email,
                        $customer->code,
                        $need_payment_amount,
                        $customer_payment_amount_vnd
                    );

                    /* tao giao dich thanh toan, thu not so tien con lai */
                    $money_charge = 0 - ($need_payment_amount - $customer_payment_amount_vnd);
                    $message = sprintf("Truy thu số tiền %s", $money_charge);

                    $create_user = User::find(Auth::user()->id);

                    UserTransaction::createTransaction(
                        UserTransaction::TRANSACTION_TYPE_ORDER_PAYMENT,
                        $message,
                        $create_user,
                        $customer,
                        $order,
                        $money_charge
                    );


//                    $customer_payment_amount = $customer_payment_amount_vnd / $order->exchange_rate;
//                    $data_fee_insert = [];
//                    $data_fee_insert[] = [ 'name' => 'customer_payment_amount', 'money' => $customer_payment_amount ];
//                    $data_fee_insert[] = [ 'name' => 'customer_payment_amount_vnd', 'money' => $customer_payment_amount_vnd ];
//                    OrderFee::createFee($order, $data_fee_insert);

                }

            }
        }
    }

    private function __kien_khong_tinh_phi(Request $request){
        die('ok');
        $packages = Package::where([
            ['weight', '>', 0],
            ['weight_type', '=', null]
        ])->get();

        if($packages){
            foreach($packages as $package){
                if(!$package instanceof Package){
                    continue;
                }
                $package->weight_type = 1;
                $package->save();

                $order = $package->getOrder();

                $this->__package_charge_fee($package, $order);

                if($order instanceof Order){
                    echo sprintf("<p>don hang <a href='%s'>%s</a></p>",
                        url('order/detail', $order->id),
                        $order->code);
                }
            }
        }
    }

    private function __package_charge_fee(Package $package, Order $order){
        $create_user = User::find(Auth::user()->id);
        $customer = User::find($order->user_id);

        $factoryMethodInstance = new ServiceFactoryMethod();

        $weight = $package->getWeightCalFee();
        $weight = $weight - 0.5;

        $service = $factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_SHIPPING_CHINA_VIETNAM,
            'weight' => $weight,
            'destination_warehouse' => $order->destination_warehouse,
            'apply_time' => $order->deposited_at,
        ]);
        $money_charge = (float)$service->calculatorFee();
        if($money_charge > 0){
            $money_charge = 0 - abs($money_charge);
        }

        $message = sprintf("Thu phí vận chuyển kiện hàng %s, số tiền %sđ", $package->logistic_package_barcode, Util::formatNumber(abs($money_charge)));

        Comment::createComment($create_user, $order, $message, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_LOG);
        Comment::createComment($create_user, $order, $message, Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_LOG);

        UserTransaction::createTransaction(
            UserTransaction::TRANSACTION_TYPE_ORDER_PAYMENT,
            $message,
            $create_user,
            $customer,
            $order,
            $money_charge,
            UserTransaction::TRANSACTION_SUB_TYPE_ORDER_PAYMENT_SHIPPING_CHINA_VIETNAM
        );
    }

    private function __tong_tien_khach_no(Request $request){
        /* tien hang */
        $a = $this->getMoneyWithName('AMOUNT_VND');

        /* mua hang */
        $b = $this->getMoneyWithName('BUYING_FEE_VND');

        /* VC nội địa TQ */

        $c = $this->getMoneyWithName('DOMESTIC_SHIPPING_FEE_VND');

        /* VC quốc tế */
        $d = $this->getMoneyWithName('SHIPPING_CHINA_VIETNAM_FEE_VND');


        /* Đóng gỗ */
        $e = $this->getMoneyWithName('WOOD_CRATING_VND');

        $ee = $a + $b + $c + $d + $e;

        /* tong thanh toan */
        $f = $this->getMoneyWithName('CUSTOMER_PAYMENT_AMOUNT_VND');

        $g = $ee > $f ? $ee - $f : 0;

        var_dump($g);

    }

    private function getMoneyWithName($name){
        $a = DB::select("select sum(money) as money 
from `order_fee` 
where `name` = '{$name}' and order_id not in (select id from `order` where `status` = 'CANCELLED');");
        return $a[0]->money;
    }

    private function __doi_soat_tien_hang(Request $request){
        $orders = Order::where([
            ['status', '!=', Order::STATUS_CANCELLED]
        ])->get();

        $amount = 0;
        if($orders){
            foreach($orders as $order){
                if(!$order instanceof Order){
                    continue;
                }

//                $amount_vnd = floatval($order->amount_vnd);
                $amount_vnd_with_items = floatval($order->amountWithItems(true));
                $deposit_percent = $order->deposit_percent;

                $amount += $amount_vnd_with_items * $deposit_percent / 100;

            }
        }

        var_dump($amount);
    }

    private function __cap_nhat_dien_thoai_nhan_hang_tren_don(Request $request){
        $limit = 500;
        $orders = Order::where([
            ['user_address_receive_phone', '=', null]
        ])->limit($limit)
            ->get();

        if($orders){
            foreach($orders as $order){
                if(!$order instanceof Order){
                    continue;
                }
                $user_address = UserAddress::find($order->user_address_id);
                if($user_address instanceof UserAddress){
                    $user_address_receive_phone = $user_address->reciver_phone;
                    $order->user_address_receive_phone = $user_address_receive_phone;
                    if($order->save()){
                        echo sprintf("don hang %s<br/><br/>", $order->code);
                    }
                }
            }
        }
    }

    private function __cap_nhat_nguoi_duoc_phan_don_cho_nhung_don_hang_cu(Request $request){
        $orders = Order::where([
            ['crane_staff_id', '=', null],
            ['status', '!=', Order::STATUS_DEPOSITED]
        ])->get();

        if($orders){
            foreach($orders as $order){
                echo sprintf("<a href='%s' target='_blank'>don hang %s (%s)</a><br/>",
                    url('order/detail', $order->id),
                    $order->code,
                    Order::getStatusTitle($order->status));

                $crane_buying = User::find($order->paid_staff_id);
                if($crane_buying instanceof User){
                    $order->crane_staff_id = $order->paid_staff_id;
                    $order->crane_staff_at = $order->bought_at;
                    $order->save();

                    echo sprintf("nguoi mua %s - %s<hr>", $crane_buying->email, $crane_buying->code);
                }

            }
        }
    }

    private function __don_truy_thu_dat_coc(Request $request){
        //select * from `user_transaction` where transaction_type = 'DEPOSIT_ADJUSTMENT' and object_type = 'ORDER';
        $user_transaction = UserTransaction::where([
            'transaction_type' => UserTransaction::TRANSACTION_TYPE_DEPOSIT_ADJUSTMENT,
            'object_type' => UserTransaction::OBJECT_TYPE_ORDER
        ])->get();

        if($user_transaction){
            foreach($user_transaction as $item){
                if(!$item instanceof UserTransaction){
                    continue;
                }

                $order = Order::find($item->object_id);
                if(!$order instanceof Order){
                    continue;
                }

                echo sprintf("<p><a href='%s'>Don hang %s</a></p>", url('order/detail', $order->id), $order->code);
            }
        }
    }

    private function __xoa_du_lieu_khach_hang(Request $request){
        $user_id = $request->get('user_id');
        $user_email = $request->get('user_email');

        $user = null;

        if($user_id){
            $user = User::where([
                'id' => $user_id
            ])->first();
        } else if($user_email){
            $user = User::where([
                'email' => $user_email
            ])->first();
        }

        if(!$user instanceof User){
            echo '<p>khong ton tai user</p>';
            exit;
        }

        #region -- Xoa lich su giao dich --
        UserTransaction::where([
            'user_id' => $user->id
        ])->delete();
        User::where([
            'id' => $user->id
        ])->update([
            'account_balance' => 0
        ]);
        #endregion

        #region -- Xoa kien hang --
        DB::statement(sprintf('delete from package_service where package_id in (select id from packages where buyer_id = %s)', $user->id));
        DB::statement(sprintf('delete from packages where buyer_id = %s', $user->id));
        #endregion

        #region -- Xoa don hang --
        DB::statement(sprintf("delete from order_service where order_id in (select id from `order` where user_id = %s)", $user->id));
        DB::statement(sprintf("delete from order_original_bill where order_id in (select id from `order` where user_id = %s)", $user->id));
        DB::statement(sprintf("delete from order_item where order_id in (select id from `order` where user_id = %s)", $user->id));
        DB::statement(sprintf("delete from order_freight_bill where order_id in (select id from `order` where user_id = %s)", $user->id));
        DB::statement(sprintf("delete from order_fee where order_id in (select id from `order` where user_id = %s)", $user->id));
        DB::statement(sprintf("delete from `order` where user_id = %s", $user->id));
        #endregion

        echo sprintf('<p>Xoa du lieu thanh cong user: %s</p>', $user->email);
    }

    private function __linh_tinh(Request $request){




//        $order_fee = OrderFee::all();
//        foreach($order_fee as $order_fee_item){
//            if(!$order_fee_item instanceof OrderFee){
//                continue;
//            }
//            $order = Order::find($order_fee_item->order_id);
//            if(!$order instanceof Order){
//                $order_fee_item->delete();
//            }
//        }

//        $exchange_rate = Exchange::getExchange();
//
//        $order = Order::find(74);
//
//        $money_vnd = 62100;
//        $money = $money_vnd / $exchange_rate;
//
//        $data_fee_insert[] = [ 'name' => 'shipping_china_vietnam_fee', 'money' => $money, 'update_money' => false ];
//        $data_fee_insert[] = [ 'name' => 'shipping_china_vietnam_fee_vnd', 'money' => $money_vnd, 'update_money' => false ];
//
//        OrderFee::createFee($order, $data_fee_insert);
//
//        $order = Order::find(70);
//
//        $money_vnd = 111000;
//        $money = $money_vnd / $exchange_rate;
//
//        $data_fee_insert[] = [ 'name' => 'shipping_china_vietnam_fee', 'money' => $money, 'update_money' => false ];
//        $data_fee_insert[] = [ 'name' => 'shipping_china_vietnam_fee_vnd', 'money' => $money_vnd, 'update_money' => false ];
//
//        OrderFee::createFee($order, $data_fee_insert);
    }

    private function __tai_chinh_khong_khop(Request $request){
        $users = User::all();
        if($users){
            foreach($users as $user){
                $account_balance_by_user_transaction = DB::table('user_transaction')
                    ->select(DB::raw('SUM(amount) as amount'))
                    ->where([
                        'user_id' => $user->id,
                        'state' => UserTransaction::STATE_COMPLETED,
                    ])
                    ->first()->amount;

                $account_balance_by_user_transaction = doubleval($account_balance_by_user_transaction);
                $user_account_balance = doubleval($user->account_balance);

                echo '<h3>Khach hang: ' . $user->email . ' - ' . $user->code . '</h3>';
                if($user_account_balance <> $account_balance_by_user_transaction){
                    echo '<p style="color: red;">Giao dịch không trùng khớp</p>';
                }
                echo '<p>So du hien tai: ' . Util::formatNumber($user_account_balance) . 'đ</p>';
                echo '<p>So du tinh theo lich su giao dich: ' . Util::formatNumber($account_balance_by_user_transaction) . 'đ</p>';

                echo '<hr>';
            }
        }
        exit;
    }

    private function __tinh_toan_lai_phi_tren_don(Request $request){
        die('huhu');
        OrderFee::truncate();

        $factoryMethodInstance = new ServiceFactoryMethod();

        $orders = Order::all();
        foreach($orders as $order){
            if(!$order instanceof Order){
                continue;
            }

            $order->save();

            $transactions = UserTransaction::where([
                'object_id' => $order->id,
                'object_type' => UserTransaction::OBJECT_TYPE_ORDER
            ])->get();

            if($transactions){
                foreach($transactions as $transaction){
                    if(!$transaction instanceof UserTransaction){
                        continue;
                    }
                    $transaction->save();
                }
            }

//            $packages = Package::where([
//                'order_id' => $order->id,
//                'is_done' => 1
//            ])->get();
//
//            if($packages){
//                foreach($packages as $package){
//                    if(!$package instanceof Package){
//                        continue;
//                    }
//
//                    $service = $factoryMethodInstance->makeService([
//                        'service_code' => Service::TYPE_SHIPPING_CHINA_VIETNAM,
//                        'weight' => $package->getWeightCalFee(),
//                        'destination_warehouse' => $order->destination_warehouse,
//                        'apply_time' => $order->deposited_at,
//                    ]);
//                    $money_charge = (float)$service->calculatorFee();
//                    if($money_charge > 0){
//                        $money_charge = 0 - abs($money_charge);
//                    }
//
//                    $data_fee_insert = [
//                        [ 'name' => 'shipping_china_vietnam_fee', 'money' => (abs($money_charge) / $order->exchange_rate), 'update_money' => true ],
//                        [ 'name' => 'shipping_china_vietnam_fee_vnd', 'money' => abs($money_charge), 'update_money' => true ],
//                    ];
//                    OrderFee::createFee($order, $data_fee_insert);
//
//                }
//            }
        }
    }

    public function index(Request $request){
        $action = '__' . $request->get('action');

        if (!method_exists($this, $action)) {
            die('Not support action!');
        }

        $result = $this->$action($request);

        die('done');
    }
}
