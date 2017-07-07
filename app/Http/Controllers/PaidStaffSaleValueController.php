<?php

namespace App\Http\Controllers;

use App\Order;
use App\Permission;
use App\User;
use App\UserPaidSaleSetting;
use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;

class PaidStaffSaleValueController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    protected $_order_receive_ids = [];

    /**
     * @author vanhs
     * @desc Lay danh sach don hang da ve cua nv mua hang
     * @param $start_month
     * @param $end_month
     * @param $crane_buying_ids
     * @return array
     */
    private function __getOrdersReceive($start_month, $end_month, $crane_buying_ids){
        $orders_overrun_list = [];

        if(!count($crane_buying_ids)){
            return $orders_overrun_list;
        }

        $crane_buying_ids_string = implode(',', $crane_buying_ids);

        $sql_orders_overrun = "
        select * from `order` 
        where 
            `received_at` >= '".$start_month."' and `received_at` <= '".$end_month."' 
            and paid_staff_id in (".$crane_buying_ids_string.") 
            and `status` in ('RECEIVED')
            and `status` not in ('CANCELLED')
        order by `received_at` asc
        ";
//        var_dump($sql_orders_overrun);

        $orders_overrun = DB::select($sql_orders_overrun);

        if($orders_overrun){
            $array_check_exits = [];
            foreach($orders_overrun as $order){
                $this->_order_receive_ids[] = $order->id;

                $order_buying_month = strtolower(date("m_Y", strtotime($order->bought_at)));
                $crane_buying_id = $order->paid_staff_id;
                $key_check = sprintf("%s_%s", $order_buying_month, $crane_buying_id);

                if(isset($array_check_exits[$key_check])){
                    $order->percent_real_cal = $array_check_exits[$key_check];
                }else{
                    $array_check_exits[$key_check] = UserPaidSaleSetting::getPercentRealCalValueWithCraneAndBuyingMonth($crane_buying_id, $order->bought_at);
                    $order->percent_real_cal = $array_check_exits[$key_check];
                }

                $order->amount_bargain_real_cal = $order->amount_bargain * $order->percent_real_cal / 100;
                $order->amount_bargain_real_cal_vnd = $order->amount_bargain_vnd * $order->percent_real_cal / 100;

                $orders_overrun_list[$order->paid_staff_id][] = $order;
            }
        }
        return $orders_overrun_list;
    }


    /**
     * @author vanhs
     * @desc Lay danh sach don hang chua ve cua nv mua hang
     * @param $start_month
     * @param $end_month
     * @param $crane_buying_ids
     * @return array
     */
    private function __getOrdersNotReceive($start_month, $end_month, $crane_buying_ids){
        $orders_buying_list = [];

        if(!count($crane_buying_ids)){
            return $orders_buying_list;
        }

        $crane_buying_ids_string = implode(',', $crane_buying_ids);

//        $sql_where = "";
//        if(count($this->_order_receive_ids)){
//            $sql_where .= sprintf(" and id not in (%s) ", implode(',', $this->_order_receive_ids));
//        }
//        $sql_orders_buying = "
//            select * from `order`
//            where
//                paid_staff_id in (".$crane_buying_ids_string.")
//                {$sql_where}
//                and `status` not in ('CANCELLED')
//            order by bought_at asc
//        ";

        $sql = "
        select * from `order` 
        where `status` not in ('CANCELLED') 
            and paid_staff_id in ({$crane_buying_ids_string}) 
            and (received_at is null or received_at > '{$end_month}') 
        order by bought_at asc;
        ";

        $orders_buying = DB::select($sql);

        if($orders_buying){
            foreach($orders_buying as $order){
                $orders_buying_list[$order->paid_staff_id][] = $order;
            }
        }
        return $orders_buying_list;
    }

    /**
     * @author vanhs
     * @desc Lay danh sach cau hinh luong cua toan bo nv mua hang
     * @return array
     */
    private function __getUsersPaidSettingList(){
        $crane_value_setting_list = [];

        $sql_crane_value_setting = "
            select * from user_paid_sale_setting
            order by id asc
        ";
        $crane_value_setting = DB::select($sql_crane_value_setting);
        if($crane_value_setting){
            foreach($crane_value_setting as $crane_value_setting_item){
                $crane_value_setting_list[$crane_value_setting_item->paid_user_id][] = $crane_value_setting_item;
            }
        }
        return $crane_value_setting_list;
    }

    /**
     * @author vanhs
     * @desc Thong ke doanh so mua hang
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function index(Request $request){
        $can_view = Permission::isAllow(Permission::PERMISSION_PAID_STAFF_SALE_VALUE);
        if(!$can_view){
            return redirect('403');
        }

        $start_month = null;
        $end_month = null;

        if($request->get('month')){
            $temp = explode('_', $request->get('month'));
            $m = $temp[0];
            $y = $temp[1];
            $d = cal_days_in_month(CAL_GREGORIAN, $m, $y);

            $start_month = sprintf("%s-%s-01", $y, $m);
            $end_month = sprintf("%s-%s-%s", $y, $m, $d);
        }

        if(empty($start_month)) $start_month = sprintf("%s-%s-01", date('Y'), date('m'));
        if(empty($end_month)) $end_month = sprintf("%s-%s-%s", date('Y'), date('m'), date('t'));

        $start_month .= ' 00:00:00';
        $end_month .= ' 23:59:59';

        $crane_buying_ids = [];
        $crane_buying_list = [];

        /**
         * Neu la chua, nguoi quan ly thi co the xem duoc doanh so cua tat ca moi nguoi
         * Neu la nv mua hang thi chi xem duoc cua ca nhan ma thoi
         */
        $permission_buying_mange = Permission::isAllow(Permission::PERMISSION_MANAGER_PAID_STAFF_SALE_VALUE);
        $current_user = User::find(Auth::user()->id);
        if($current_user->isGod() || $permission_buying_mange){
            $crane_buying_list = User::getListCraneBuying();
        }else{
            $crane_buying_list[] = User::find(Auth::user()->id);
        }

        $crane_value_setting_list = $this->__getUsersPaidSettingList($start_month, $end_month);

        if($crane_buying_list){
            foreach($crane_buying_list as $crane_buying_list_item){
                $crane_buying_list_item->setting = UserPaidSaleSetting::getSettingWithCranePaidId($crane_buying_list_item->id, $start_month);
                $crane_buying_list_item->crane_value_setting =
                    isset($crane_value_setting_list[$crane_buying_list_item->id])
                    ? $crane_value_setting_list[$crane_buying_list_item->id] : [];

                //tong tien bao khach trong thang
                $crane_buying_list_item->amount_customer_current_month_vnd = UserPaidSaleSetting::getOrderAmountWithCranePaidAndMonth(
                    $crane_buying_list_item->id,
                    'customer_amount_vnd',
                    $start_month,
                    $end_month
                );
                //tong tien thuc mua trong thang
                $crane_buying_list_item->amount_original_current_month_vnd = UserPaidSaleSetting::getOrderAmountWithCranePaidAndMonth(
                    $crane_buying_list_item->id,
                    'amount_original_vnd',
                    $start_month,
                    $end_month
                );

                //tien mac ca trong thang
//                $crane_buying_list_item->amount_bargain_current_month_vnd = UserPaidSaleSetting::getOrderAmountWithCranePaidAndMonth(
//                    $crane_buying_list_item->id,
//                    'amount_bargain_vnd',
//                    $start_month,
//                    $end_month
//                );
                $crane_buying_list_item->amount_bargain_current_month_vnd = $crane_buying_list_item->amount_customer_current_month_vnd
                    - $crane_buying_list_item->amount_original_current_month_vnd;

                //phan tram mac ca trong thang
                $crane_buying_list_item->percent_bargain_current_month = 0;
                if($crane_buying_list_item->amount_customer_current_month_vnd){
                    $crane_buying_list_item->percent_bargain_current_month = $crane_buying_list_item->amount_bargain_current_month_vnd * 100
                        / $crane_buying_list_item->amount_customer_current_month_vnd;
                }

                //dat chi tieu mac ca trong thang hay khong?
                $crane_buying_list_item->is_bargain_target = false;
                if($crane_buying_list_item->setting
                    && $crane_buying_list_item->percent_bargain_current_month >= $crane_buying_list_item->setting->require_min_bargain_percent){
                    $crane_buying_list_item->is_bargain_target = true;
                }

                //phan tram thuc tinh doanh so
                $crane_buying_list_item->rose_percent_real_cal = 0;
                if($crane_buying_list_item->setting){
                    $crane_buying_list_item->rose_percent_real_cal = $crane_buying_list_item->is_bargain_target
                        ? $crane_buying_list_item->setting->rose_percent : $crane_buying_list_item->setting->rose_percent_min;
                }

                $crane_buying_ids[] = $crane_buying_list_item->id;
            }
        }

        //don hang da ve
        $orders_overrun_list = $this->__getOrdersReceive($start_month, $end_month, $crane_buying_ids);

        //don hang chua ve
        $orders_buying_list = $this->__getOrdersNotReceive($start_month, $end_month, $crane_buying_ids);

        return view('paid_staff_sale_value', [
           'page_title' => 'Doanh số, lương mua hàng',
            'crane_buying_list' => $crane_buying_list,
            'orders_buying_list' => $orders_buying_list,
            'orders_overrun_list' => $orders_overrun_list,
            'crane_value_setting_list' => $crane_value_setting_list,
            'begin_year' => date('Y'),
            'end_year' => (date('Y') + 10),
            'start_month' => $start_month,
            'end_month' => $end_month
        ]);

    }

    /**
     * @author vanhs
     * @desc Luu cau hinh luong cua nhan vien mua hang
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setting(Request $request){
        $can_save = Permission::isAllow(Permission::PERMISSION_SETUP_PAID_STAFF_SALE_VALUE);
        if(!$can_save){
            return response()->json(['success' => false, 'message' => 'ban khong co quyen thuc hien hanh dong nay']);
        }

        try{
            DB::beginTransaction();
            $data = [];
            $items = $request->get('items');
            foreach($items as $item){
                $activated_at = sprintf("%s-%s-01 00:00:00", $item['start_year'], $item['start_month']);
                $day_of_month = cal_days_in_month(CAL_GREGORIAN, $item['end_month'], $item['end_year']);
                $deadlined_at = sprintf("%s-%s-%s 23:59:59", $item['end_year'], $item['end_month'], $day_of_month);
                $data[] = [
                    'paid_user_id' => $request->get('paid_user_id'),
                    'activated_at' => $activated_at,
                    'deadlined_at' => $deadlined_at,
                    'salary_basic' => $item['salary_basic'],
                    'rose_percent' => $item['rose_percent'],
                    'rose_percent_min' => $item['rose_percent_min'],
                    'require_min_bargain_percent' => $item['require_min_bargain_percent'],
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }
            DB::statement(sprintf("delete from user_paid_sale_setting 
                                      where paid_user_id = %s", $request->get('paid_user_id')));

            UserPaidSaleSetting::insert($data);
            DB::commit();
            return response()->json(['success' => true, 'message' => '']);
        }catch (Exception $e){
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'co loi xay ra, vui long thu lai']);
        }

    }
}
