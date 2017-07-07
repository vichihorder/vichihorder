<?php

namespace App\Http\Controllers;

use App\Exchange;
use App\Jobs\SendSms;
use App\Library\ServiceFee\AbstractService;
use App\Library\ServiceFee\Buying;
use App\Library\ServiceFee\ServiceFactoryMethod;
use App\OrderFee;
use App\Package;
use App\PackageService;
use App\Service;
use App\OrderFreightBill;
use App\Order;
use App\SystemConfig;
use App\User;
use App\Role;
use App\UserRole;
use App\Permission;
use App\UserTransaction;
use App\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function homeStatistic(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        if(!Util::validateDate($start_date, 'Y-m-d')){
            return response()->json(['success' => false, 'message' => sprintf('Định dạng ngày bắt đầu %s không chính xác', $start_date)]);
        }
        if(!Util::validateDate($end_date, 'Y-m-d')){
            return response()->json(['success' => false, 'message' => sprintf('Định dạng ngày kết thúc %s không chính xác', $end_date)]);
        }

        $html = '';
        $can_view_statistic_money_quick = Permission::isAllow(Permission::PERMISSION_STATISTIC_QUICK);
        if($can_view_statistic_money_quick){
            $view = View::make('home_statistic_tpl', [
                'statistic' => $this->__statisticMoneyQuick($start_date, $end_date)
            ]);
            $html = $view->render();
        }

        return response()->json(['success' => true, 'message' => '', 'html' => $html]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        $user_id = Auth::user()->id;

        //============phi mua hang===========
//        $factoryMethodInstance = new ServiceFactoryMethod();
//        $service = $factoryMethodInstance->makeService([
//            'service_code' => Service::TYPE_BUYING,
//            'total_amount' => 60000000,
//            'apply_time' => '2017-04-30 00:12:12'
//        ]);
//        $buying_fee = doubleval($service->calculatorFee());
//        var_dump($buying_fee);


//        $factoryMethodInstance = new ServiceFactoryMethod();
//
//        $service = $factoryMethodInstance->makeService([
//            'service_code' => Service::TYPE_SHIPPING_CHINA_VIETNAM,
//            'weight' => 70,
//            'destination_warehouse' => 'S-SG',
//            'apply_time' => '2017-04-26 10:00:00',
//        ]);
//        $money_charge = (float)$service->calculatorFee();
//        var_dump($money_charge);

        /*

        $factoryMethodInstance = new ServiceFactoryMethod();

        $order_id = 19;

        //============phi mua hang===========
        $service = $factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_BUYING,
            'total_amount' => 6000,
            'apply_time' => '2017-09-01 00:00:00'
        ]);
        var_dump('phi mua hang');
        var_dump($service->calculatorFee());

        //============phi kiem hang===========
        $order = Order::find($order_id);
        $total_quantity_items_normal = $order->getItemNormalQuantity();
        $total_quantity_items_assess = $order->getItemAssessQuantity();

        $service = $factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_CHECKING,
            'total_quantity_items_normal' => $total_quantity_items_normal,
            'total_quantity_items_assess' => $total_quantity_items_assess,
            'apply_time' => '2017-09-01 00:00:00'
        ]);
        var_dump('phi kiem hang');
        var_dump($service->calculatorFee());

        //============phi van chuyen TQ-VN===========
        $order = Order::find($order_id);
        $destination_warehouse = $order->destination_warehouse;
        $destination_warehouse = 'BT-SG';

        //-- Can nang truyen vao phai chuyen sang kg
        $service = $factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_SHIPPING_CHINA_VIETNAM,
            'weight' => 10,
            'destination_warehouse' => $destination_warehouse,
            'apply_time' => '2017-09-01 00:00:00'
        ]);
        var_dump('phi van chuyen TQ - VN');
        var_dump($service->calculatorFee());

        //============phi dong go===========
        $service = $factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_WOOD_CRATING,
            'fee_shipping_china_vietnam' => 50000,//phi van chuyen quoc te
            'weight_manual' => 10,//can nang tinh
            'weight_equivalent' => 50,//can nang quy doi
            'calculator_by' => 'MANUAL',//MANUAL hoac EQUIVALENT
            'apply_time' => '2017-09-01 00:00:00'
        ]);
        var_dump('phi dong go');
        var_dump($service->calculatorFee());

        */

        // Store a piece of data in the session...

//        $order_freight_bill = Order::find(3)->freight_bill;
//        $order_original_bill = Order::find(3)->original_bill;
//
//        $role = User::find(2)->role;
//

//        $user_permission = Cache::get("user_permission_{$user_id}");
//        var_dump($user_permission);

//        die('hello');

//        $orders = Order::all();
//        foreach($orders as $order){
//            $order->save();
//        }

//        $order = Order::find(83);
//        $order->domestic_shipping_fee = 9;
//        $order->save();

//        $job = new SendSms([]);
//        dispatch($job);



        $current_user = User::find($user_id);

        $total_order_deposit_today = 0;
        $total_customer_register_today = 0;

        if($current_user->section == User::SECTION_CRANE){
            $total_order_deposit_today = Order::getTotalDepositByDay(date('Y-m-d'));
            $total_customer_register_today = User::getTotalRegisterByDay(date('Y-m-d'));
        }

        $permission = [
            'can_view_statistic_money_quick' => Permission::isAllow(Permission::PERMISSION_STATISTIC_QUICK),
            'can_view_statistic_money_detail' => Permission::isAllow(Permission::PERMISSION_STATISTIC_DETAIL),
        ];

        return view('home', [
            'page_title' => 'Trang chủ',
            'current_user' => $current_user,
            'total_order_deposit_today' => $total_order_deposit_today,
            'total_customer_register_today' => $total_customer_register_today,
            'permission' => $permission,
        ]);
    }

    /**
     * @author vanhs
     * @desc Thong ke tai chinh chung tren bang chung
     * @param $start_today
     * @param $end_today
     * @return array
     */
    private function __statisticMoneyQuick($start_today, $end_today){
        $statistic = [];

        $start_today = sprintf("%s 00:00:00", $start_today);
        $end_today = sprintf("%s 23:59:59", $end_today);

//        var_dump($start_today);
//        var_dump($end_today);

        $query = DB::table('user_transaction')
            ->select(DB::raw('sum(amount) as amount'))
            ->where([
                ['state', '=', UserTransaction::STATE_COMPLETED],
                ['transaction_type', '=', UserTransaction::TRANSACTION_TYPE_ADJUSTMENT],
                ['user_id', '!=', User::USER_ID_TEST],
                ['created_at', '>=', $start_today],
                ['created_at', '<=', $end_today],
                ['amount', '>', 0]
            ])
            ->first();

        $customer_recharge_amount = 0;

        if($query){
            $customer_recharge_amount = $query->amount;
        }

        $statistic[] = [
            'name' => 'Tiền khách nạp',
//            'value' => Util::formatNumber($customer_recharge_amount),
            'money' => $customer_recharge_amount
        ];

        $query1 = DB::select(sprintf("
            select sum(order_fee.money) as 'total' 
            from order_fee 
            inner join `order` 
                on `order`.id = order_fee.order_id
            where order_fee.`name` = 'AMOUNT_VND' 
                and `order`.`deposited_at` >= '%s'
                and `order`.`deposited_at` <= '%s'
                and `order`.`status` != '%s';
        ", $start_today, $end_today, Order::STATUS_CANCELLED));
        $amount_vnd = $query1[0]->total;

        $statistic[] = [
            'name' => 'Tiền hàng (1)',
            'money' => $amount_vnd
        ];

        $query1 = DB::select(sprintf("
            select sum(order_fee.money) as 'total' 
            from order_fee 
            inner join `order` 
                on `order`.id = order_fee.order_id
            where order_fee.`name` = 'DEPOSIT_AMOUNT_VND' 
                and `order`.`deposited_at` >= '%s'
                and `order`.`deposited_at` <= '%s'
                and `order`.`status` != '%s';
        ", $start_today, $end_today, Order::STATUS_CANCELLED));
        $deposit_amount_vnd = $query1[0]->total;

        $statistic[] = [
            'name' => 'Tiền đặt cọc (2)',
            'money' => $deposit_amount_vnd
        ];

//        $statistic[] = [
//            'name' => 'Tiền còn thiếu (3=1-2)',
//            'money' => ($amount_vnd - $deposit_amount_vnd)
//        ];

        return $statistic;
    }
}
