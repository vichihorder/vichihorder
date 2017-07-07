<?php
/**
 * Created by PhpStorm.
 * User: goerge
 * Date: 13/06/2017
 * Time: 13:51
 */

namespace App\Http\Controllers;


use App\Order;
use App\OrderFee;
use App\Package;
use App\User;
use Illuminate\Http\Request;
use PHPExcel;
use PHPExcel_IOFactory;

class AccountingFinanceControlCustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * @param Request $request
     * @return array
     */
    public function index(Request $request){

        $user_id = $request->get('user_id'); // lay ra user khach
        $time_to = $request->get('time_to');
        $time_from = $request->get('time_from');
        $order_status = $request->get('order_status');

        $condition = [];
        if(!empty($user_id)){
            $condition['username'] = $user_id;
        }
        if(!empty($time_from)){
            $condition['time_from'] = $time_from;
        }

        if(!empty($time_to)){
            $condition['time_to'] = $time_to;
        }

        if(!empty($order_status)){
            $condition['status'] = $order_status;
        }else{
            $condition['status'] = '';
        }


        $arr_data = [];
        $user = User::where('id',$user_id)->first();

        if(!$user_id){
            $arr_data = $this->exportUnFollowUser();
            $this->exportExcel($arr_data);
        }else{
            if ($user instanceof User){
                $arr_data = $this->exportByUser($condition);

                $this->exportExcel($arr_data);
            }
        }

        return view('accounting_finance',[
            'data' => $arr_data,
            'order_status' => Order::$statusLevel,
            'page_title' => 'Báo cáo sản lượng hàng tháng',
        ]);
    }

    /**
     * lấy tất cả các đơn của tất cả các user
     * @param array $condition
     * @return array
     * @internal param $condition
     */
    private function exportUnFollowUser($condition = []){
        $arr_data = [];
        if(count($condition) == 0){
            return   $arr_data;
        }

        $list_user = User::where([
            'section' => User::SECTION_CUSTOMER,
            'status' => User::STATUS_ACTIVE
        ])->get();

        $list_user_id = [];
        foreach ($list_user as $item_user){
            /** @var $item_user User */
            $list_user_id[] = $item_user->id;
        }

        $whereData = [];


        if($condition['status'] == Order::STATUS_DEPOSITED) {
            $whereData = [
                ['deposited_at', '>=', $condition['time_to']],
                ['deposited_at', '<', $condition['time_from']],
                ['status', Order::STATUS_DEPOSITED]
            ];
        }
        if($condition['status'] == Order::STATUS_BOUGHT){
            $whereData = [
                ['bought_at','>=', $condition['time_to']],
                ['bought_at','<', $condition['time_from']],
                ['status', Order::STATUS_BOUGHT]
            ];
        }

        if($condition['status'] == Order::STATUS_SELLER_DELIVERY){
            $whereData = [
                ['seller_delivery_at','>=', $condition['time_to']],
                ['seller_delivery_at','<', $condition['time_from']],
                ['status', Order::STATUS_SELLER_DELIVERY]
            ];
        }

        if($condition['status'] == Order::STATUS_RECEIVED_FROM_SELLER){
            $whereData = [
                    ['seller_delivery_at','>=', $condition['time_to']],
                    ['seller_delivery_at','<', $condition['time_from']],
                    ['status', Order::STATUS_RECEIVED_FROM_SELLER]
                ];
        }
        if($condition['status'] == Order::STATUS_TRANSPORTING){
            $whereData = [
                ['transporting_at','>=', $condition['time_to']],
                ['transporting_at','<', $condition['time_from']],
                ['status', Order::STATUS_TRANSPORTING]
            ];
        }

        if($condition['status'] == Order::STATUS_WAITING_DELIVERY){
            $whereData = [
                ['waiting_delivery_at','>=', $condition['time_to']],
                ['waiting_delivery_at','<', $condition['time_from']],
                ['status', Order::STATUS_WAITING_DELIVERY]
            ];
        }
        if($condition['status'] == Order::STATUS_DELIVERING){
            $whereData = [
                ['delivering_at','>=', $condition['time_to']],
                ['delivering_at','<', $condition['time_from']],
                ['status', Order::STATUS_DELIVERING]
            ];
        }

        if($condition['status'] == Order::STATUS_RECEIVED){
            $whereData = [
                ['received_at','>=', $condition['time_to']],
                ['received_at','<', $condition['time_from']],
                ['status', Order::STATUS_RECEIVED]
            ];
        }
        
        
        // lay gia don hang theo user
        $list_order = Order::where($whereData)
        ->whereIn('user_id',$list_user_id)->get();

        foreach ($list_order as $item_order){
            if($item_order->status == Order::STATUS_CANCELLED){
                continue;
            }

            $arr_data[] = $this->orderFee($item_order);
        }

        return $arr_data;

    }

    /**
     * lấy dữ liệu theo user
     * @param $condition
     * @return array
     * @internal param User $user
     */
    private function exportByUser($condition){

        $arr_data = [];

        $whereData = [];


        if($condition['status'] ){
            if($condition['status'] == Order::STATUS_DEPOSITED) {
                $whereData = [
                    ['deposited_at', '>=', $condition['time_to']],
                    ['deposited_at', '<', $condition['time_from']],
                    ['status', Order::STATUS_DEPOSITED],
                    ['user_id',$condition['username']]
                ];
            }
            if($condition['status'] == Order::STATUS_BOUGHT){
                $whereData = [
                    ['bought_at','>=', $condition['time_to']],
                    ['bought_at','<', $condition['time_from']],
                    ['status', Order::STATUS_BOUGHT],
                    ['user_id',$condition['username']]
                ];
            }

            if($condition['status'] == Order::STATUS_SELLER_DELIVERY){
                $whereData = [
                    ['seller_delivery_at','>=', $condition['time_to']],
                    ['seller_delivery_at','<', $condition['time_from']],
                    ['status', Order::STATUS_SELLER_DELIVERY],
                    ['user_id',$condition['username']]
                ];
            }

            if($condition['status'] == Order::STATUS_RECEIVED_FROM_SELLER){
                $whereData = [
                    ['seller_delivery_at','>=', $condition['time_to']],
                    ['seller_delivery_at','<', $condition['time_from']],
                    ['status', Order::STATUS_RECEIVED_FROM_SELLER],
                    ['user_id',$condition['username']]
                ];
            }
            if($condition['status'] == Order::STATUS_TRANSPORTING){
                $whereData = [
                    ['transporting_at','>=', $condition['time_to']],
                    ['transporting_at','<', $condition['time_from']],
                    ['status', Order::STATUS_TRANSPORTING],
                    ['user_id',$condition['username']]
                ];
            }

            if($condition['status'] == Order::STATUS_WAITING_DELIVERY){
                $whereData = [
                    ['waiting_delivery_at','>=', $condition['time_to']],
                    ['waiting_delivery_at','<', $condition['time_from']],
                    ['status', Order::STATUS_WAITING_DELIVERY],
                    ['user_id',$condition['username']]
                ];
            }
            if($condition['status'] == Order::STATUS_DELIVERING){
                $whereData = [
                    ['delivering_at','>=', $condition['time_to']],
                    ['delivering_at','<', $condition['time_from']],
                    ['status', Order::STATUS_DELIVERING],
                    ['user_id',$condition['username']]
                ];
            }

            if($condition['status'] == Order::STATUS_RECEIVED){
                $whereData = [
                    ['received_at','>=', $condition['time_to']],
                    ['received_at','<', $condition['time_from']],
                    ['status', Order::STATUS_RECEIVED],
                    ['user_id',$condition['username']]
                ];
            }
        }else{
            $whereData = [
                ['user_id',$condition['username']]
            ];
        }


//        if(!in_array($condition['status'],Order::$statusLevel)){
//
//        }

        $list_order = Order::where($whereData)
            ->whereNotIn('status',[Order::STATUS_CANCELLED])
            ->get();

        foreach ($list_order as $item_order){
            $arr_data[] = $this->orderFee($item_order);
        }

        return $arr_data;
    }

    /**
     * ham tinh toan ra phi cua mot don hang
     * @param $order Order
     * @return array
     */
    private function orderFee($order){
        // mang array
        $arr_data = [];

        $packages = Package::where(['order_id' => $order->id,'is_deleted' => 0])->get();

        $package_weight_payment = 0;
        foreach ($packages as $item_package){
            /** @var $item_package Package */
            $package_weight_payment += $item_package->getWeightCalFee();
        }

        $username = User::where('id',$order->user_id)->first();
        if($username instanceof  User){
            $username_email = $username->email;
        }else{
            $username_email = '';
        }

        // lấy ra các khoản của khách
        $order_fees = OrderFee::where('order_id', $order->id)->get();

        $arr_data['customer_email'] = $username_email;


        $amount_fee = 0 ; // tien hang
        $buying_fee = 0; // phi mua hang
        $domistic_fee = 0 ; // phi van chuyen noi dia
        $customer_paymented = 0; // tien hang khach da tra
        $without_money = 0; //truy thu tren don
        $refund_order = 0; // tien tra lai
        $wood_crating_fee = 0; // phi dong go
        $shipping_fee = 0; // phi van chuyen quoc te
        $khach_no = 0;

        foreach ($order_fees as $item_fee) {

            /** @var $item_fee OrderFee */

            if ($item_fee->name == OrderFee::AMOUNT_VND) {
                $amount_fee += $item_fee->money;

            }
            if ($item_fee->name == OrderFee::BUYING_FEE_VND) {
                $buying_fee += $item_fee->money;
            }
            if ($item_fee->name == OrderFee::DOMESTIC_SHIPPING_FEE_VND) {
                $domistic_fee += $item_fee->money;
            }
            if ($item_fee->name == OrderFee::CUSTOMER_PAYMENT_AMOUNT_VND) {
                $customer_paymented += $item_fee->money;
            }
            if ($item_fee->name == OrderFee::WITHDREW_ORDER_VND) {
                $without_money += $item_fee->money;
            }
            if ($item_fee->name == OrderFee::REFUND_ORDER_VND) {
                $refund_order += $item_fee->money;
            }
            if ($item_fee->name == OrderFee::WOOD_CRATING_VND) {
                $wood_crating_fee += $item_fee->money;
            }
            if ($item_fee->name == OrderFee::SHIPPING_CHINA_VIETNAM_FEE_VND) {
                $shipping_fee += $item_fee->money;
            }

            $total_fee = $amount_fee + $buying_fee + $domistic_fee + $shipping_fee + $wood_crating_fee;
            $khach_no = $total_fee - $customer_paymented;
        }
        $arr_data['order_code'] = $order->code;
        $arr_data['order_status'] = $order->status;
        $arr_data['amount_fee'] =  $amount_fee;
        $arr_data['buying_fee'] =  $buying_fee;
        $arr_data['domistic_fee'] =  $domistic_fee;
        $arr_data['customer_paymented'] =  $customer_paymented;
        $arr_data['without_money'] =  $without_money;
        $arr_data['refund_order'] =  $refund_order;
        $arr_data['refund_order'] =  $refund_order;
        $arr_data['wood_crating_fee'] =  $wood_crating_fee;
        $arr_data['shipping_fee'] =  $shipping_fee;
        $arr_data['khach_no'] =  $khach_no;
        $arr_data['package_weight_payment'] = $package_weight_payment;
        $arr_data['deposited_time'] = $order->deposited_at;

        return $arr_data;
    }
    /**
     * ham xuat excel chinh
     * @param $arr_data
     * @return string
     * @throws \Exception
     */
    private function exportExcel($arr_data){
        if(count($arr_data) == 0){
            return "KHông có dữ liệu";
        }
        $excel_order = new PHPExcel();

        $excel_order->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Tên Khách Hàng')
            ->setCellValue('B1', 'Mã Đơn')
            ->setCellValue('C1', 'Tiền Hàng (VND)')
            ->setCellValue('D1', 'Phí Mua Hàng (VND)')
            ->setCellValue('E1', 'Phí Vận chuyển Nội địa (VND)')
            ->setCellValue('F1', 'Cân nặng đơn (kg)')
            ->setCellValue('G1', 'Trạng thái đơn')
            ->setCellValue('H1', 'Tiền khách đã thanh toán (VND)')
            ->setCellValue('I1', 'Tiền Khách nợ (VND)')
            ->setCellValue('J1', 'Truy Thu trên đơn (VND)')
            ->setCellValue('K1', 'Tiền trả lại (VND)')
            ->setCellValue('L1', 'Tiền đóng gỗ (VND)')
            ->setCellValue('M1','Thời gian đặt cọc')
        ;
        $i = 2;
        foreach ($arr_data as $item_data) {
            $excel_order->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $item_data['customer_email'])
                ->setCellValue('B'.$i, $item_data['order_code'])
                ->setCellValue('C'.$i, $item_data['amount_fee'])
                ->setCellValue('D'.$i, $item_data['buying_fee'])
                ->setCellValue('E'.$i, $item_data['domistic_fee'])
                ->setCellValue('F'.$i, $item_data['package_weight_payment'])
                ->setCellValue('G'.$i, Order::$statusTitle[$item_data['order_status']])
                ->setCellValue('H'.$i, $item_data['customer_paymented'])
                ->setCellValue('I'.$i, $item_data['khach_no'])
                ->setCellValue('J'.$i, $item_data['without_money'])
                ->setCellValue('K'.$i, $item_data['refund_order'])
                ->setCellValue('L'.$i, $item_data['wood_crating_fee'])
                ->setCellValue('M'.$i, $item_data['deposited_time'])
            ;

            $excel_order->getActiveSheet()->getStyle('C'.$i)->getNumberFormat()->setFormatCode('#,##0');
            $excel_order->getActiveSheet()->getStyle('D'.$i)->getNumberFormat()->setFormatCode('#,##0');
            $excel_order->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode('#,##0');
            $excel_order->getActiveSheet()->getStyle('H'.$i)->getNumberFormat()->setFormatCode('#,##0');
            $excel_order->getActiveSheet()->getStyle('I'.$i)->getNumberFormat()->setFormatCode('#,##0');
            $excel_order->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()->setFormatCode('#,##0');
            $excel_order->getActiveSheet()->getStyle('K'.$i)->getNumberFormat()->setFormatCode('#,##0');
            $excel_order->getActiveSheet()->getStyle('L'.$i)->getNumberFormat()->setFormatCode('#,##0');
            // end trong for
            $i++;
        }
        $name = 'TÀI CHÍNH - KHÁCH NỢ - KẾ TOÁN' . "-" .date('Y-m-d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $name . '.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($excel_order, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }


}