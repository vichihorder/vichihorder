<?php
/**
 * Created by PhpStorm.
 * User: goerge
 * Date: 03/06/2017
 * Time: 09:41
 */

namespace App\Http\Controllers;


use App\Order;
use App\OrderFee;
use App\Package;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * kiểm tra trạng thái của đơn hàng
     * @param $order_id
     * @return bool
     */
    private static function checkStatusOrder($order_id){
        $order = Order::where('id',$order_id)->first();
        if($order instanceof Order){
            if($order->status == Order::STATUS_CANCELLED){
                return false;
            }else{
                return true;
            }
        }

        return false;
    }

    /**
     * hàm thống kê dựa trên điêu kiện kho và thời gian
     */
    public function index(Request $request){
        $time_from = $request->get('date1');
        $time_to = $request->get('date2'); //
        $warehouse = $request->get('warehouse');// kho hien tai
        $warehouse_status = $request->get('warehouse_status'); // trang thái xuất hay nhập kh
        
        $packages = Package::select('*');

        $packages = $packages->orderBy('id', 'desc');
        $packages = $packages->where('is_deleted',0);

        if($warehouse){
            $packages = $packages->where('current_warehouse',$warehouse);
        }
        // thoi gian chinh la thoi gian nhap kho
        if($warehouse_status){
            $packages = $packages->where('warehouse_status',$warehouse_status);
        }
        if($time_from){
            if(!$warehouse_status || $warehouse_status == 'IN' ){
                $packages = $packages->where('warehouse_status_in_at','>=',$time_from." 00:00:00");
            }elseif($warehouse_status == 'OUT'){
                $packages = $packages->where('warehouse_status_out_at','>=',$time_from." 00:00:00");
            }
        }
        if($time_to){
            if(!$warehouse_status || $warehouse_status == 'IN'){
                $packages = $packages->where('warehouse_status_in_at','<',$time_to." 23:59:59");
            }elseif($warehouse_status == 'OUT'){
                $packages = $packages->where('warehouse_status_out_at','<',$time_to." 23:59:59");
            }
            
        }

        $packages = $packages->get();
        $total_package = $packages->count(); // tong so luong kien hang

        // tinh tong trong luong hang hoa van chuyen
        $package_weight = 0;
        // lay cac ma don tuong ung
        $order_id = [];
        foreach ($packages as $item_package){
            /** @var $item_package Package */
            $package_weight += $item_package->getWeightCalFee();

            $order_id[] = $item_package->order_id;
        }

        $list_order_id = array_unique($order_id);



        /**
         * phí của đơn hàng, được tính ở đây
         */
        $order_fee = OrderFee::whereIn('order_id',$list_order_id)->get();

        // tiền hàng
        $customer_payment_order = 0;
        // tiền ship nội địa
        $domestic_shipping_fee = 0;
        // thu phi 1% dich vu
        $buying_fee = 0;

        /**
         * lấy giá trị
         */
        foreach ($order_fee as $item_domistic_shipping){

            /** @var $item_domistic_shipping OrderFee */
            // tien hang
            if($item_domistic_shipping->name == OrderFee::AMOUNT_VND){
                
                $customer_payment_order += $item_domistic_shipping->money;
            }
            // tien phi ship noi dia
            if($item_domistic_shipping->name == OrderFee::DOMESTIC_SHIPPING_FEE_VND){
                $domestic_shipping_fee += $item_domistic_shipping->money;
            }
            // phi mua hang
            if($item_domistic_shipping->name == OrderFee::BUYING_FEE_VND){
                $buying_fee += $item_domistic_shipping->money;
            }

        }
        return view('report',[
            'package_weight' =>$package_weight ,
            'total_package' => $total_package,
            'customer_payment_order' => $customer_payment_order,
            'total_buying_fee' => $buying_fee,
            'total_domictic_shipping_fee' => $domestic_shipping_fee,
            'page_title' => 'Báo cáo sản lượng hàng tháng',
        ]);

    }

}