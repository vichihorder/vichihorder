<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Jobs\SendSms;
use App\Library\ServiceFee\ServiceFactoryMethod;
use App\Order;
use App\OrderFee;
use App\OrderFreightBill;
use App\Package;
use App\PackageService;
use App\Scan;
use App\SendEmailCustomerQueue;
use App\SendSmsToCustomer;
use App\Service;
use App\SystemConfig;
use App\User;
use App\UserAddress;
use App\UserTransaction;
use App\Util;
use App\WareHouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class ScanController extends Controller
{
    protected $action_error = [];

    protected $message = null;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexs()
    {
        $data = $this->__getInitData('layouts.app');

        return view('scan', $data);
    }

    public function statistic(Request $request){
        $warehouse_list = WareHouse::getAllWarehouse();

        $view = View::make('scan_statistic', [
            'action_list' => Scan::$action_list,
            'warehouse_list' => $warehouse_list,
        ]);
        $html = $view->render();

        return response()->json(['success' => true, 'html' => $html]);
    }

    private function __getInitData($layout = null){
        $warehouse_list = WareHouse::getAllWarehouse();

//        $history_scan_list = Scan::findByUser(Auth::user()->id);
        $history_scan_list = null;

        return [
            'action_list' => Scan::$action_list,
            'warehouse_list' => $warehouse_list,
            'history_scan_list' => $history_scan_list,
            'page_title' => 'Quét mã vạch',
            'layout' => $layout
        ];
    }

    /**
     * @author vanhs
     * @desc Cac hanh dong tren trang quet ma vach
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function action(Request $request)
    {

        try{
            DB::beginTransaction();

            $currentUser = User::find(Auth::user()->id);
            $action = '__' . $request->get('action');
            $warehouse = WareHouse::retrieveByCode($request->get('warehouse'));

            if(empty($request->get('barcode'))){
                return response()->json(['success' => false, 'message' => 'Vui lòng nhập vào mã quét!']);
            }

            if(!$warehouse || !$warehouse instanceof WareHouse){
                return response()->json(['success' => false, 'message' => sprintf('Kho %s không tồn tại!', $request->get('warehouse'))]);
            }

            //check barcode exists
            $package = Package::retrieveByCode($request->get('barcode'));
            if(!$package instanceof Package){
                return response()->json(['success' => false, 'message' => sprintf('Không tìm thấy kiện hàng với mã %s', $request->get('barcode'))]);
            }

            if (!method_exists($this, $action)) {
                return response()->json(['success' => false, 'message' => 'Not support action!']);
            }

            $result = $this->$action($request, $warehouse, $currentUser);
            if(!$result){
                return response()->json( ['success' => false, 'message' => implode('<br>', $this->action_error)] );
            }

            DB::commit();

            $html = null;
            if($request->get('response')){
                $view = View::make($request->get('response'), $this->__getInitData('layouts/app_blank'));
                $html = $view->render();
            }

            return response()->json([
                'success' => true,
                'message' => $this->message,
                'html' => $html,
                'result' => $result,
            ]);

        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại' . $e->getMessage()]);
        }

    }

    /**
     * @author vanhs
     * @desc Logic
     * - Nhap kho Trung Quoc
     *      + Chuyen trang thai kien sang "NhatMinh247 nhận"
     *      + Chuyen trang thai don hang sang "NhatMinh247 nhận" (neu la kien dau tien nhap kho TQ)
     * - Nhap kho phan phoi tai Viet Nam
     *      + Chuyen trang thai kien hang sang "Cho giao hang"
     *      + Gui tin nhan thong bao hang ve kho (neu la kien dau tien nhap kho phan phoi)
     *      + Chuyen trang thai don hang sang "Cho giao hang"
     *
     * @param Request $request
     * @param WareHouse $warehouse
     * @param User $currentUser
     * @return bool|array
     */
    private function __in(Request $request, WareHouse $warehouse, User $currentUser){
        $barcode = $request->get('barcode');
        $create_user = User::find(Auth::user()->id);
        $response = [];

        if($warehouse->type == WareHouse::TYPE_RECEIVE){
            $message_internal = sprintf("Kiện hàng %s nhập kho %s", $barcode, $warehouse->code);

            $package = Package::where([
                'logistic_package_barcode' => $barcode,
                'status' => Package::STATUS_INIT,
            ])->first();

            if($package && $package instanceof Package){
                $package->inputWarehouseReceive($warehouse->code);

                $order = Order::find($package->order_id);
                if($order instanceof Order){
                    $order->changeOrderReceivedFromSeller();
                    $order_address = $order->getCustomerReceiveAddress();
                    $user_phone = $order_address->phone;
                    $response = array(
                        'barcode' => $barcode,
                        'address' => $order_address,
                        'message' => " nhập kho ".$warehouse->code,
                        'phone' => $user_phone,
                        'status' => 'success',
                        'order_id' => $order->id
                    );
                    Comment::createComment($create_user, $order, $message_internal, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);
                }
            }

            $this->message = $message_internal;
            return $response;

        }else if($warehouse->type == WareHouse::TYPE_DISTRIBUTION){

            $message_internal = sprintf("Kiện hàng %s nhập kho phân phối %s", $barcode, $warehouse->code);

            $package = Package::where([
                'logistic_package_barcode' => $barcode,
                'status' => Package::STATUS_TRANSPORTING,
                'warehouse_status' => Package::WAREHOUSE_STATUS_OUT,
            ])->first();

            if($package instanceof Package){
                $package->inputWarehouseDistribution($warehouse->code);

                $order = Order::find($package->order_id);
                if($order instanceof Order){
                    // trong truong hop kho nhan hàng trùng với kho đích trên đơn thì mới
                    // chuyển trạng thái sang chờ giao hàng
                    if($warehouse->code == $order->destination_warehouse){
                        $order->changeOrderWaitingDelivery();
                        $user_address = UserAddress::find($order->user_address_id);
                        if($user_address instanceof UserAddress){
                            // lay ra so tien cua khach hang , neu so tien khach
                            $user_customer = User::find($order->user_id);
                            $account_banace = $user_customer->account_balance;
                            $without_money = abs($account_banace);
                            if($warehouse->code == 'K-HN'){
                                $name_house = 'Hà Nội';
                            }else{
                                $name_house = 'Sài Gòn';
                            }
                            if($account_banace < 0){

                                $content = "Nhatminh247: Kiện hàng {$barcode} của đơn {$order->code} nhập kho phân phối "
                                    . $name_house. " .Bạn cần nạp thêm tiền để lấy hàng ! ";
                            }else{
                                $content = "Nhatminh247: Kiện hàng {$barcode} của đơn {$order->code} nhập kho phân phối ". $name_house .".Mời bạn đến kho để lấy hàng .";
                            }

                            #region lưu vào bảng gửi sms
                                $array_data = [
                                    'phone' => $user_address->reciver_phone,
                                    'content' => $content,
                                    'order_id' => $order->id,
                                    'user_id' => $user_address->user_id,
                                    'send_status' => SendSmsToCustomer::NOT_YET
                                ];
                            $smsToCustomer = new SendSmsToCustomer();
                            $smsToCustomer->CustomerSms($array_data);
                            #endregion
                            #region lưu vào bảng gửi mail queue
                                $array_data_email = [
                                    'order_id' => $order->id,
                                    'email' => User::find($user_address->user_id)->email,
                                    'content' => $content,
                                    'user_id' => $user_address->user_id,
                                    'send_status' => SendEmailCustomerQueue::NOT_YET
                                ];

                                $email_to_customer = new SendEmailCustomerQueue();
                                $email_to_customer->EmailQueueOrder($array_data_email);
                            #endregion --luu queue guiwr mail--
                        }

                        $order_address = $order->getCustomerReceiveAddress();
                        $user_phone = $order_address->phone;
                        $response = array(
                            'barcode' => $barcode,
                            'address' => $order_address,
                            'phone' => $user_phone,
                            'message' => ' nhập kho phân phối '. $warehouse->code,
                            'status' => 'success',
                            'order_id' => $order->id
                        );
                    }
                    Comment::createComment($create_user, $order, $message_internal, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

                }
            }

            $this->message = $message_internal;
            return $response;
        }

        $this->__writeActionScanLog($request, $warehouse, $currentUser);

        return true;
    }

    /**
     * @author vanhs
     * @desc Logic
     * - Xuat kho Trung Quoc
     *      + Chuyen trang thai kien hang sang "Van Chuyen"
     *      + Chuyen trang thai don hang sang "Van Chuyen" (neu la kien dau tien xuat kho TQ)
     *      + Thu phi kien, neu la kien chuyen thang
     * - Xuat kho phan phoi tai Viet Nam
     *      + Chuyen trang thai kien hang sang "Dang giao hang"
     *      + Chuyen trang thai don hang sang "Dang giao hang" (neu la kien dau tien xuat kho phan phoi tai VN)
     *      + Thu phi kien, neu khong phai kien chuyen thang
     *
     * @param Request $request
     * @param WareHouse $warehouse
     * @param User $currentUser
     * @return bool
     */
    private function __out(Request $request, WareHouse $warehouse, User $currentUser){
        $barcode = $request->get('barcode');
        $create_user = User::find(Auth::user()->id);
        $response = [];
        if($warehouse->type == WareHouse::TYPE_RECEIVE){
            $message_internal = sprintf("Kiện hàng %s xuất kho %s", $barcode, $warehouse->code);

            $package = Package::where([
                'logistic_package_barcode' => $barcode,
                'status' => Package::STATUS_RECEIVED_FROM_SELLER,
                'warehouse_status' => Package::WAREHOUSE_STATUS_IN,
                'current_warehouse' => $warehouse->code,
            ])->first();
            if($package && $package instanceof Package){
                $package->outputWarehouseReceive($warehouse->code);

                $order = Order::find($package->order_id);
                if($order instanceof Order){
                    $customer = User::find($order->user_id);
                    if(!$order->changeOrderTransporting()){
                        $this->action_error[] = sprintf('Không chuyển trang thái đơn hàng sang %s', Order::getStatusTitle(Order::STATUS_TRANSPORTING));
                        return false;
                    }

                    Comment::createComment($create_user, $order, $message_internal, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

                    if($package->isTransportStraight()){
                        $this->__packageChargeFee($package, $order,
                            $create_user, $customer);

                        $user_address = UserAddress::find($order->user_address_id);
                        if($user_address instanceof UserAddress){
                            $job = (new SendSms([
                                'phone' => $user_address->reciver_phone,
                                'content' => sprintf('Don hang %s da bat dau van chuyen ve VN', $order->code)
                            ]));
                            dispatch($job);
                        }
                    }

                    $order_address = $order->getCustomerReceiveAddress();
                    $user_phone = $order_address->phone;
                    $response = array(
                        'barcode' => $barcode,
                        'address' => $order_address,
                        'phone' => $user_phone,
                        'message' => ' xuất kho '. $warehouse->code,
                        'status' => 'success',
                        'order_id' => $order->id
                    );

                }
            }

            $this->message = $message_internal;

            return $response;

        }else if($warehouse->type == WareHouse::TYPE_DISTRIBUTION){
            $message_internal = sprintf("Kiện hàng %s xuất kho phân phối %s", $barcode, $warehouse->code);

            $package = Package::where([
                'logistic_package_barcode' => $barcode,
                'status' => Package::STATUS_WAITING_FOR_DELIVERY,
                'warehouse_status' => Package::WAREHOUSE_STATUS_IN,
                'current_warehouse' => $warehouse->code
            ])->first();

            if($package instanceof Package){
                $package->outputWarehouseDistribution($warehouse->code);

                $order = Order::find($package->order_id);
                if($order instanceof Order){
                    $customer = User::find($order->user_id);
                    if(!$order->changeOrderDelivering()){
                        $this->action_error[] = sprintf('Không chuyển trang thái đơn hàng sang %s', Order::getStatusTitle(Order::STATUS_DELIVERING));
                        return false;
                    }

                    Comment::createComment($create_user, $order, $message_internal, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

                    if(!$package->isTransportStraight()){
                        $this->__packageChargeFee($package, $order, $create_user, $customer);
                    }

                    $order_address = $order->getCustomerReceiveAddress();
                    $user_phone = $order_address->phone;
                    $response = array(
                        'barcode' => $barcode,
                        'address' => $order_address,
                        'phone' => $user_phone,
                        'message' => ' xuất kho phân phối '. $warehouse->code,
                        'status' => 'success',
                        'order_id' => $order->id
                    );
                }
            }

            $this->message = $message_internal;

            return $response;
        }

        $this->__writeActionScanLog($request, $warehouse, $currentUser);
        return true;
    }

    /**
     * @author vanhs
     * @desc Thu phi kien hang
     * @param Package $package
     * @param Order $order
     * @param User $create_user
     * @param User $customer
     */
    private function __packageChargeFee(Package $package, Order $order,
                                        User $create_user, User $customer){
        $factoryMethodInstance = new ServiceFactoryMethod();

        $service = $factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_SHIPPING_CHINA_VIETNAM,
            'weight' => $package->getWeightCalFee(),
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

        if($package->existService(Service::TYPE_WOOD_CRATING)){
            $factoryMethodInstance = new ServiceFactoryMethod();
            //============phi dong go===========
            $service = $factoryMethodInstance->makeService([
                'exchange_rate' => $order->exchange_rate,
                'weight' => $package->getWeightCalFee(),
                'service_code' => Service::TYPE_WOOD_CRATING
            ]);
            $wood_crating_vnd = $service->calculatorFee();

            $message = sprintf("Thu phí đóng gỗ kiện hàng %s, số tiền %sđ", $package->logistic_package_barcode, Util::formatNumber($wood_crating_vnd));

            Comment::createComment($create_user, $order, $message, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_LOG);
            Comment::createComment($create_user, $order, $message, Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_LOG);

            if($wood_crating_vnd > 0){
                $wood_crating_vnd = 0 - $wood_crating_vnd;
            }

            UserTransaction::createTransaction(
                UserTransaction::TRANSACTION_TYPE_ORDER_PAYMENT,
                $message,
                $create_user,
                $customer,
                $order,
                $wood_crating_vnd,
                UserTransaction::TRANSACTION_SUB_TYPE_ORDER_PAYMENT_WOOD_CRATING
            );
        }

        $package->setDone();
    }

    private function __writeActionScanLog(Request $request, WareHouse $warehouse, User $currentUser){
        $scan = new Scan();
        $scan->barcode = $request->get('barcode');
        $scan->action = $request->get('action');
        $scan->warehouse = $request->get('warehouse');
        $scan->created_by = $currentUser->id;
        return $scan->save();
    }
}
