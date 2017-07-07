<?php
/**
 * Created by PhpStorm.
 * User: goerge
 * Date: 10/06/2017
 * Time: 11:32
 */

namespace App\Http\Controllers;


use App\Order;
use App\OrderFee;
use App\Package;
use App\User;
use Illuminate\Http\Request;
use PHPExcel;
use PHPExcel_IOFactory;

class ExportExcelFinaceController extends Controller
{
    /**
     * ham tinh toan xuat ra excel lai tren don
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function exportExcelOrderFee(Request $request){

        $order_id = $request->get('order_id'); // lay gia tri id don hang

        $order = Order::findOneByIdOrCode($order_id);

        if($order instanceof Order) {
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
                ->setCellValue('L1', 'Tiền đóng gỗ (VND)');



            // tinh tong can nang cua don hang
            $packages = Package::where(['order_id' => $order_id,'is_deleted' => 0])->get();

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
            $order_fees = OrderFee::where('order_id', $order_id)->get();



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
                // tổng phí đơn hàng
            }
            $excel_order->setActiveSheetIndex(0)
                ->setCellValue('A2',$username_email)
                ->setCellValue('B2',$order->code)
                ->setCellValue('C2',$amount_fee)
                ->setCellValue('D2',$buying_fee)
                ->setCellValue('E2',$domistic_fee)
                ->setCellValue('F2',$package_weight_payment)
                ->setCellValue('G2',Order::$statusTitle[$order->status])
                ->setCellValue('H2',$customer_paymented)
                ->setCellValue('I2',$khach_no)
                ->setCellValue('J2',$without_money)
                ->setCellValue('K2',$refund_order)
                ->setCellValue('L2',$wood_crating_fee)

            ;
            $excel_order->getActiveSheet()->getStyle('C2')->getNumberFormat()->setFormatCode('#,##0');
            $excel_order->getActiveSheet()->getStyle('D2')->getNumberFormat()->setFormatCode('#,##0');
            $excel_order->getActiveSheet()->getStyle('E2')->getNumberFormat()->setFormatCode('#,##0');
            $excel_order->getActiveSheet()->getStyle('H2')->getNumberFormat()->setFormatCode('#,##0');
            $excel_order->getActiveSheet()->getStyle('I2')->getNumberFormat()->setFormatCode('#,##0');
            $excel_order->getActiveSheet()->getStyle('J2')->getNumberFormat()->setFormatCode('#,##0');
            $excel_order->getActiveSheet()->getStyle('K2')->getNumberFormat()->setFormatCode('#,##0');
            $excel_order->getActiveSheet()->getStyle('L2')->getNumberFormat()->setFormatCode('#,##0');

            $name = 'TÀI CHÍNH ĐƠN HÀNG' . "-" .$order->code;
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


}