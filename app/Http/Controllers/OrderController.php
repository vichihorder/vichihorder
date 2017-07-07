<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Comment;
use App\CustomerNotification;
use App\Exchange;
use App\Location;
use App\OrderFee;
use App\OrderFreightBill;
use App\OrderItem;
use App\OrderOriginalBill;
use App\OrderService;
use App\Package;
use App\Permission;
use App\Service;
use App\SystemConfig;
use App\User;
use App\UserAddress;
use App\UserOriginalSite;
use App\UserRole;
use App\UserTransaction;
use App\Util;
use App\WareHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;

class OrderController extends Controller
{

    protected $action_error = [];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getOrdersData(){
        $current_user = User::find(Auth::user()->id);
        /** @var User $current_user */

        $per_page = 20;

        $params = Input::all();

        $orders = Order::select('*');
        $orders = $orders->orderBy('id', 'desc');

        if(!empty($params['original_bill'])){
            $query = DB::table('order_original_bill')
                ->select(DB::raw('GROUP_CONCAT(order_id) as order_id'))
                ->where([
                    ['original_bill', '=', $params['original_bill']],
                    ['is_deleted', '=', 0],
                ])
                ->first();
            if($query){
                $o_ids = explode(',', $query->order_id);
            }else{
                $o_ids[] = 0;
            }
            $orders = $orders->whereIn('id', $o_ids);
        }

        if(!empty($params['order_not_fights'])){
            $orders = $orders->where('crane_staff_id', null);
        }

        if(!empty($params['user_address_receive_phone'])){
            $orders = $orders->where('user_address_receive_phone', 'like', '%' . $params['user_address_receive_phone'] . '%');
        }

        if(!empty($params['freight_bill'])){
            $query = DB::table('order_freight_bill')
                ->select(DB::raw('GROUP_CONCAT(order_id) as order_id'))
                ->where([
                    ['freight_bill', '=', $params['freight_bill']],
                    ['is_deleted', '=', 0],
                ])
                ->first();
            if($query){
                $o_ids = explode(',', $query->order_id);
            }else{
                $o_ids[] = 0;
            }
            $orders = $orders->whereIn('id', $o_ids);
        }

        if(!empty($params['order_code'])){
            $orders = $orders->where('code', $params['order_code']);
        }

        if(!empty($params['paid_staff_id'])){
            $orders = $orders->where('paid_staff_id', $params['paid_staff_id']);
        }

        if(!empty($params['customer_code_email'])){
            $user_ids = User::where(function($query) use ($params){
//                $query->where('code', '=', $params['customer_code_email'])
//                    ->orWhere('email', '=', $params['customer_code_email']);

                $query->where('id', '=', $params['customer_code_email']);

            })->pluck('id');
            $orders = $orders->whereIn('user_id', $user_ids);
        }

        if(!empty($params['status'])){
            $orders = $orders->whereIn('status', explode(',', $params['status']));
        }
        $total_orders = $orders->count();
        $orders = $orders->paginate($per_page);
        $orders->withPath('order');

        $order_ids = [];
        if($total_orders){
            foreach($orders as $order){
                if(!$order instanceof Order){
                    continue;
                }

                $order->paid_staff = null;
                if($order->paid_staff_id){
                    $order->paid_staff = User::find($order->paid_staff_id);
                }

                $order_ids[] = $order->id;

                $customer = User::find($order->user_id);

                $fee = $order->fee();

                $order_fee = [];
                foreach(OrderFee::$fee_field_order_detail as $key => $label){
                    $value = $key;
                    if(isset($fee[$key])){
                        $value = Util::formatNumber($fee[$key]);
                    }
                    $order_fee[] = [
                        'label' => $label,
                        'value' => $value,
                    ];
                }

                $order->customer = $customer;
                $order->order_fee = $order_fee;
            }
        }

        $services = [];
        $services_order = OrderService::findByOrderIds($order_ids);
        foreach($services_order as $service_order){
            if(!$service_order || !$service_order instanceof OrderService){
                continue;
            }
            $services[$service_order->order_id][] = [
                'code' => $service_order['service_code'],
                'name' => Service::getServiceName($service_order['service_code']),
                'icon' => Service::getServiceIcon($service_order['service_code']),
            ];
        }

        $crane_buying_list = [];
        $can_set_crane_buying = Permission::isAllow(Permission::PERMISSION_ORDER_BUYING_CAN_SET_CRANE_STAFF)
            || $current_user->isGod();

        if($can_set_crane_buying){
            $crane_buying_list = UserRole::findByRoleId(
                [ SystemConfig::getConfigValueByKey('group_crane_buying_id') ]
            );
        }

        $view = View::make('orders_data', [
            'total_orders' => $total_orders,
            'orders' => $orders,
            'can_set_crane_buying' => $can_set_crane_buying,
            'crane_buying_list' => $crane_buying_list
        ]);
        $html = $view->render();

        return response()->json([
            'html' => $html,
            'success' => true,
            'message' => null
        ]);
    }

    /**
     * @author vanhs
     * @desc Danh sach don hang
     * @param Request $request
     * @return View
     */
    public function orders(Request $request){

//        $crane_buying_list = UserRole::findByRoleId(
//            [ SystemConfig::getConfigValueByKey('group_crane_buying_id') ]
//        );
//
//        //default is today
//        $start_time = sprintf('% 00-00-00', date('Y-m-d'));
//        $end_time = sprintf('%s 23:59:59', date('Y-m-d'));
//
//        $users = [];
//        if($crane_buying_list){
//            foreach($crane_buying_list as $crane_buying_list_item){
//                //don duoc phan
//                $crane_buying_list_item->order_receive = DB::table('order')
//                    ->select('id')
//                    ->where([
//                        ['crane_staff_id', '=', $crane_buying_list_item->id],
//                        ['crane_staff_at', '>=', $start_time],
//                        ['crane_staff_at', '<=', $end_time],
//                    ])->count();
//
//                //don da mua
//                $crane_buying_list_item->order_buying = DB::table('order')
//                    ->select('id')
//                    ->where([
//                        ['paid_staff_id', '=', $crane_buying_list_item->id],
//                        ['bought_at', '>=', $start_time],
//                        ['bought_at', '<=', $end_time],
//                    ])->count();
//
//
//
//
//
////                $crane_buying_list_item->order_receive = 0;//don duoc phan
////                $crane_buying_list_item->order_buying = 0;//don da mua
//                $crane_buying_list_item->order_amount = 0;//tien hang
//                $crane_buying_list_item->order_buying_time_avg = 0;//thoi gian mua trung binh tinh tu luc duoc phan don
//
//                $users[] = $crane_buying_list_item;
//            }
//        }

        $status_list = [];
        foreach(Order::$statusTitle as $key => $val){
            $selected = false;
            if(!empty($request->get('status'))){
                $selected = in_array($key, explode(',', $request->get('status')));
            }
            $status_list[] = [
                'key' => $key,
                'val' => $val,
                'selected' => $selected
            ];
        }

        $crane_buying_list = UserRole::findByRoleId(
            [ SystemConfig::getConfigValueByKey('group_crane_buying_id') ]
        );

        return view('orders', [
            'page_title' => ' Quản lý đơn hàng',
            'status_list' => $status_list,
            'crane_buying_list' => $crane_buying_list
        ]);
    }



    /**
     * @author vanhs
     * @desc Chi tiet don hang
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function order(Request $request){

        $can_view = Permission::isAllow(Permission::PERMISSION_ORDER_VIEW);
        if(!$can_view):
            return redirect('403');
        endif;

        $order_id = $request->route('id');
        $order = Order::findOneByIdOrCode($order_id);
        if(!$order):
            return redirect('404');
        endif;

        $customer = User::find($order->user_id);
        if(!$customer || !$customer instanceof User){
            return redirect('404');
        }

        return view('order_detail', $this->__getOrderInitData($order, $customer, 'layouts.app'));
    }

    private function __getOrderInitData(Order $order, User $customer, $layout){

        $current_user = User::find(Auth::user()->id);
        /** @var User $current_user */

        $order_item_comments_data = [];
        $order_item_comments = Order::findByOrderItemComments($order->id);
        if($order_item_comments){
            foreach($order_item_comments as $order_item_comment){
                $order_item_comment->user = User::find($order_item_comment->user_id);
                $order_item_comments_data[$order_item_comment->object_id][] = $order_item_comment;
            }
        }

        $permission = [
            'can_change_order_bought' => $order->status == Order::STATUS_DEPOSITED
                && ($order->crane_staff_id == $current_user->id || $current_user->isGod()),

            'can_change_order_cancel' =>
                    ( $order->isBeforeStatus(Order::STATUS_TRANSPORTING) && $order->crane_staff_id == $current_user->id )
                || ( $order->isBeforeStatus(Order::STATUS_TRANSPORTING) && $current_user->isGod() )
                || ( $order->isBeforeStatus(Order::STATUS_TRANSPORTING) && Permission::isAllow(Permission::PERMISSION_ORDER_BUYING_CAN_SET_CRANE_STAFF) ),

            'can_change_order_received_from_seller' => $order->status == Order::STATUS_SELLER_DELIVERY,
            'can_change_order_item_quantity' => $order->isBeforeStatus(Order::STATUS_BOUGHT)|| $current_user->isGod(),
            'can_change_order_item_price' => $order->isBeforeStatus(Order::STATUS_BOUGHT) || $current_user->isGod(),
            'can_change_order_account_purchase_origin' => $order->isBeforeStatus(Order::STATUS_BOUGHT),

            'can_change_order_domestic_shipping_fee' => $order->isBeforeStatus(Order::STATUS_BOUGHT) || $current_user->isGod(),

            'can_change_order_deposit_percent' => $order->isBeforeStatus(Order::STATUS_BOUGHT),
            'can_view_package_list' => Permission::isAllow(Permission::PERMISSION_PACKAGE_LIST_VIEW),
            'can_add_freight_bill_to_order' => Permission::isAllow(Permission::PERMISSION_ORDER_ADD_FREIGHT_BILL)
                && $order->isAfterStatus(Order::STATUS_BOUGHT, true),
        ];

        $packages = $order->package()->where([
            'is_deleted' => 0,
        ])
            ->whereNotIn('status', [ Package::STATUS_INIT ])
            ->get();

        $fee = $order->fee();

        $order_fee = [];
        foreach(OrderFee::$fee_field_order_detail as $key => $label){
            $value = $key;
            if(isset($fee[$key])){
                $value = Util::formatNumber($fee[$key]);
            }
            $order_fee[] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        $order_service = [];
        $o_services = $order->service;
        foreach($o_services as $o_service){
            if(!$o_service || !$o_service instanceof OrderService){
                continue;
            }
            $order_service[] = $o_service->service_code;
        }

        $services = [];
        $services_list = Service::getAllService();
        if($services_list){
            foreach($services_list as $service){
                if(!$service || !$service instanceof Service){
                    continue;
                }
                $services[] = [
                    'code' => $service->code,
                    'name' => Service::getServiceName($service->code),
                    'is_default' => Service::checkIsDefault($service->code),
                    'checked' => in_array($service->code, $order_service) ? true : false
                ];
            }
        }



        return [
            'packages' => $packages,
            'order_id' => $order->id,
            'freight_bill' => $this->__order_freight_bill_list($order),
            'original_bill' => $order->original_bill()->where([ 'is_deleted' => 0 ])->get(),
            'warehouse_distribution' => WareHouse::findByType(WareHouse::TYPE_DISTRIBUTION),
            'warehouse_receive' => WareHouse::findByType(WareHouse::TYPE_RECEIVE),
            'user_address' => $order->getCustomerReceiveAddress(),
            'order' => $order,
            'services' => $services,
            'order_item_comments' => $order_item_comments_data,
            'user_origin_site' => UserOriginalSite::all(),
            'order_items' => $order->item,
            'order_fee' => $order_fee,
            'customer' => $customer,
            'transactions' => Order::findByTransactions($order->id),
            'page_title' => 'Chi tiết đơn hàng',
            'permission' => $permission,
            'layout' => $layout,
        ];
    }

    private function __order_freight_bill_list(Order $order){
        $freight_bill = $order->freight_bill()->where([ 'is_deleted' => 0 ])->get();
        $freight_bill_list = [];
        if($freight_bill){
            foreach($freight_bill as $freight_bill_item){
                if(!$freight_bill_item instanceof OrderFreightBill){
                    continue;
                }
                $orders_list = [];
                $orders_freight_bill = OrderFreightBill::where([
                    [ 'freight_bill', '=', $freight_bill_item->freight_bill ],
                    [ 'is_deleted', '=', 0 ],
                    [ 'order_id', '<>', $order->id ]
                ])->get();
                foreach($orders_freight_bill as $order_freight_bill){
                    $orders_list[] = Order::find($order_freight_bill->order_id);
                }
                $freight_bill_item->orders = $orders_list;
                $freight_bill_list[] = $freight_bill_item;
            }
        }
        return $freight_bill_list;
    }

    /**
     * @author vanhs
     * @desc Cac hanh dong tren trang chi tiet don hang
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function action(Request $request)
    {

        try{
            DB::beginTransaction();

            $order_id = $request->route('id');
            $order = Order::find($order_id);
            $user = User::find(Auth::user()->id);
            $action = '__' . $request->get('action');

            if(!$order || !$order instanceof Order){
                return response()->json(['success' => false, 'message' => 'Order not found!']);
            }

            if(!$user || !$user instanceof User){
                return response()->json(['success' => false, 'message' => 'User not found!']);
            }

            $customer = User::find($order->user_id);

            if(!$customer || !$customer instanceof User){
                return response()->json(['success' => false, 'message' => 'Customer not found!']);
            }

            if (!method_exists($this, $action)) {
                return response()->json(['success' => false, 'message' => 'Not support action!']);
            }

//            if($order->isEndingStatus()){
//                return response()->json(['success' => false, 'message' => sprintf('Đơn hàng hiện đã ở trạng thái cuối (%s), không thể thay đổi thông tin!', Order::getStatusTitle($order->status))]);
//            }

            $result = $this->$action($request, $order, $user);
            if(!$result){
                return response()->json( ['success' => false, 'message' => implode('<br>', $this->action_error)] );
            }

            DB::commit();
            $html = null;

            if($request->get('response')){
                $view = View::make($request->get('response'), $this->__getOrderInitData($order, $customer, 'layouts/app_blank'));
                $html = $view->render();
            }


            return response()->json([
                'success' => true,
                'message' => 'success',
                'html' => $html
            ]);

        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại']);
        }

    }

    /**
     * @author vanhs
     * @desc Ham cap nhat cac thong ve gia, phi van chuyen goc tren trang TQ
     * @param Request $request
     * @param Order $order
     * @param User $current_user
     * @return bool
     */
    private function __change_money_original(Request $request, Order $order, User $current_user){
        if($request->get('name') == 'amount_original'){

            $order->amount_original = $request->get('money');
            Comment::createComment($current_user, $order,
                sprintf("Cập nhật tổng giá thực mua %s¥", $request->get('money')),
                Comment::TYPE_INTERNAL,
                Comment::TYPE_CONTEXT_ACTIVITY);

        }else if($request->get('name') == 'domestic_shipping_china_original'){

//            $order->domestic_shipping_china_original = $request->get('money');
//            Comment::createComment($current_user, $order,
//                sprintf("Cập nhật phí VC nội dịa gốc %s¥", $request->get('money')),
//                Comment::TYPE_INTERNAL,
//                Comment::TYPE_CONTEXT_ACTIVITY);


        }
        return $order->save();
    }

    private function __insert_freight_bill(Request $request, Order $order, User $user){

        $freight_bill = $request->get('freight_bill');

        $can_execute = Permission::isAllow(Permission::PERMISSION_ORDER_ADD_FREIGHT_BILL);
        if(!$can_execute):
            $this->action_error[] = 'Not permission!';
            return false;
        endif;

        if(!$order->isAfterStatus(Order::STATUS_BOUGHT, true)){
            $this->action_error[] = sprintf('Không được phép thêm mã vận đơn khi đơn ở trạng thái %s', Order::getStatusTitle($order->status));
            return false;
        }

        if(empty($freight_bill)):
            $this->action_error[] = 'Mã vận đơn không để trống!';
            return false;
        endif;

        $order_freight_bill_exists = $order->has_freight_bill($freight_bill);

        if($order_freight_bill_exists):
            $this->action_error[] = sprintf('Mã hóa đơn %s đã tồn tại', $freight_bill);
            return false;
        endif;

//        $freight_bill_exists = OrderFreightBill::where([
//            'freight_bill' => $freight_bill,
//            'is_deleted' => 0,
//        ])->count();

        $order->create_freight_bill($user->id, $freight_bill);

//        $message = '';
//        if($freight_bill_exists):
//            $message = sprintf('Mã hóa đơn %s đã tồn tại ở 1 đơn hàng khác!', $freight_bill);
//        endif;

        Comment::createComment($user, $order, sprintf('Thêm mã vận đơn %s', $freight_bill), Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

        $order_empty_freight_bill = $order->exist_freight_bill();

        if($order_empty_freight_bill
            && $order->status == Order::STATUS_BOUGHT){
            $order->changeStatus(Order::STATUS_SELLER_DELIVERY);
            $status_title_after_change = Order::getStatusTitle(Order::STATUS_SELLER_DELIVERY);
            Comment::createComment($user, $order, sprintf("Đơn hàng chuyển sang trạng thái %s", $status_title_after_change), Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_LOG);
            Comment::createComment($user, $order, sprintf("Chuyển trạng thái đơn sang %s", $status_title_after_change), Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_LOG);
            # tạo notification cho khách
            $title = sprintf("Thay đổi trạng thái trên đơn %s", $order->code);
            CustomerNotification::notificationCustomer($order,$title,sprintf("Đơn hàng chuyển sang trạng thái %s", $status_title_after_change),'ORDER');

        }

        return true;
    }

    /**
     * bổ sung notification khi chọn đơn hàng
     * @param Request $request
     * @param Order $order
     * @param User $user
     * @return bool
     */
    private function __choose_service(Request $request, Order $order, User $user){
        $service = $request->get('service');
        if(empty($service)){
            $this->action_error[] = 'Chưa chọn dịch vụ!';
        }

        $can_execute = Permission::isAllow(Permission::PERMISSION_ORDER_REMOVE_FREIGHT_BILL);
        if(!$can_execute):
            $this->action_error[] = 'Not permission!';
        endif;

        if(count($this->action_error)){
            return false;
        }

        if(in_array($service, Service::getServiceDefault())){
            return true;
        }

        $message = null;
        $exist_service = $order->existService($service);
        if($request->get('checkbox') == 'check'){
            if(!$exist_service){
                $order_service = new OrderService();
                $order_service->order_id = $order->id;
                $order_service->service_code = $service;
                $order_service->save();

                $message = sprintf("Chọn dịch vụ %s", Service::getServiceName($service));


                $title = "Chọn dịch vụ trên đơn ". $order->code;
                $content = $user->name . " chọn dịch vụ " .Service::getServiceName($service)." đơn ".$order->code;
                CustomerNotification::notificationCustomer($order,$title,$content,'ORDER');
                
            }
        }else{
            if($exist_service){
                OrderService::where([
                    'order_id' => $order->id,
                    'service_code' => $service
                ])->delete();

                $message = sprintf("Bỏ chọn dịch vụ %s", Service::getServiceName($service));
                $title = sprintf("Bỏ chọn dịch vụ trên đơn %s", $order->code);
                $content = $user->name . " bỏ chọn dịch vụ " .Service::getServiceName($service)." đơn ".$order->code;;
                CustomerNotification::notificationCustomer($order,$title,$content,'ORDER');
            }
        }

        
        Comment::createComment($user, $order, $message, Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);
        Comment::createComment($user, $order, $message, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

        return true;
    }

    private function __remove_freight_bill(Request $request, Order $order, User $user){

        $freight_bill = $request->get('freight_bill_delete');

        $can_execute = Permission::isAllow(Permission::PERMISSION_ORDER_REMOVE_FREIGHT_BILL);
        if(!$can_execute):
            $this->action_error[] = 'Not permission!';
        endif;

        if(count($this->action_error)){
            return false;
        }

        OrderFreightBill::where([
            'order_id' => $order->id,
            'freight_bill' => $freight_bill
        ])->update([
            'is_deleted' => 1,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        Comment::createComment($user, $order, sprintf('Xóa mã vận đơn %s', $freight_bill), Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

        return true;
    }

    private function __insert_original_bill(Request $request, Order $order, User $user){
        $original_bill = $request->get('original_bill');

        $can_execute = Permission::isAllow(Permission::PERMISSION_ORDER_ADD_ORIGINAL_BILL);
        if(!$can_execute):
            $this->action_error[] = 'Not permission!';
        endif;

        if(empty($original_bill)):
            $this->action_error[] = 'Mã hóa đơn gốc không để trống!';
        endif;

        $order_original_bill_exists = $order->has_original_bill($original_bill);

        if($order_original_bill_exists):
            $this->action_error[] = sprintf('Mã hóa đơn gốc %s đã tồn tại!', $original_bill);
        endif;

        if(count($this->action_error)){
            return false;
        }

//        $original_bill_exists = OrderOriginalBill::where([
//            'original_bill' => $original_bill,
//            'is_delete' => 0,
//        ])->count();

        $order->create_original_bill($user->id, $original_bill);

        Comment::createComment($user, $order, sprintf('Thêm mã hóa đơn gốc %s', $original_bill), Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

//        $message = '';
//        if($original_bill_exists):
//            $message = sprintf('Mã hóa đơn gốc %s đã tồn tại ở 1 đơn hàng khách!', $original_bill);
//        endif;

        return true;
    }

    private function __remove_original_bill(Request $request, Order $order, User $user){

        $original_bill = $request->get('original_bill_delete');

        $can_execute = Permission::isAllow(Permission::PERMISSION_ORDER_REMOVE_ORIGINAL_BILL);
        if(!$can_execute):
            $this->action_error[] = 'Not permission!';
        endif;

        if(count($this->action_error)){
            return false;
        }

        OrderOriginalBill::where([
            'order_id' => $order->id,
            'original_bill' => $original_bill
        ])->update([
            'updated_at' => date('Y-m-d H:i:s'),
            'is_deleted' => 1
        ]);

        Comment::createComment($user, $order, sprintf('Xóa mã hóa đơn gốc %s', $original_bill), Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

        return true;
    }

    /**
     * @author vanhs
     * @desc Them/bo dich vu tren don
     * @param Request $request
     * @param Order $order
     * @param User $user
     * @return bool
     */
    private function __change_order_service(Request $request, Order $order, User $user){

        $action_check = $request->get('action_check');
        $service_code = $request->get('service_code');
        $service_name = $request->get('service_name');

        $exists_service = $order->existService($service_code);
        switch ($action_check){
            case 'check':
                if(!$exists_service){
                    OrderService::addService($order->id, $service_code);

                    Comment::createComment($user, $order, sprintf("Chọn dịch vụ %s", $service_name), Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);
                    Comment::createComment($user, $order, sprintf("Chọn dịch vụ %s", $service_name), Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

                    $title = "Chọn dịch vụ trên đơn ". $order->code;
                    $content = $user->name . " chọn dịch vụ " .$service_name;
                    CustomerNotification::notificationCustomer($order,$title,$content,'ORDER');


                }
                break;
            case 'uncheck':
                if($exists_service){
                    OrderService::removeService($order->id, $service_code);

                    Comment::createComment($user, $order, sprintf("Bỏ chọn dịch vụ %", $service_name), Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);
                    Comment::createComment($user, $order, sprintf("Bỏ chọn dịch vụ %", $service_name), Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

                    $title = "Bỏ dịch vụ trên đơn ".$order->code;
                    $content = $user->name . " bỏ chọn dịch vụ " .$service_name;
                    CustomerNotification::notificationCustomer($order,$title,$content,'ORDER');
                }
                break;
        }
        return true;
    }

    /**
     * @author vanhs
     * @desc Thay doi so luong san pham
     * @param Request $request
     * @param Order $order
     * @param User $user
     * @return bool
     */
    private function __change_order_item_quantity(Request $request, Order $order, User $user){
        $item_id = $request->get('item_id');
        $order_item = OrderItem::find($item_id);

        if($user->isGod()){

        }else{
            if(!$order->isBeforeStatus(Order::STATUS_BOUGHT)){
                $this->action_error[] = 'Không được phép sửa số lượng sản phẩm ở trạng thái ' . Order::getStatusTitle($order->status);
            }
        }



        if(!$order_item || !$order_item instanceof OrderItem){
            $this->action_error[] = 'Sản phẩm #' . $item_id . ' không tồn tại!';
        }

        if(count($this->action_error)){
            return false;
        }

        $old_order_quantity = $order_item->order_quantity;
        $new_order_quantity = (int)$request->get('order_quantity');
        $order_item->order_quantity = $new_order_quantity;
        $order_item->save();

        $order->total_order_quantity = $order->total_order_quantity();
        $order->amount = $order->amountWithItems();
        $order->save();

        if($old_order_quantity <> $new_order_quantity){
            Comment::createComment(
                $user,
                $order_item,
                sprintf("Sửa số lượng sản phẩm từ %s thành %s", $old_order_quantity, $new_order_quantity),
                Comment::TYPE_NONE,
                Comment::TYPE_CONTEXT_ACTIVITY,
                $order
            );

            $title = "Sửa số lượng sản phẩm trên đơn ".$order->code;
            $message = sprintf(" sửa số lượng sản phẩm từ %s thành %s", $old_order_quantity, $new_order_quantity);
            $content = $user->name. $message;
            CustomerNotification::notificationCustomer($order,$title,$content,'ORDER');
        }


        return true;
    }

    /**
     * @author vanhs
     * @desc Thay doi gia san pham
     * @param Request $request
     * @param Order $order
     * @param User $user
     * @return bool
     */
    private function __change_order_item_price(Request $request, Order $order, User $user){
        $item_id = $request->get('item_id');
        $order_item = OrderItem::find($item_id);

        if($user->isGod()){

        }else{
            if(!$order->isBeforeStatus(Order::STATUS_BOUGHT)){
                $this->action_error[] = 'Không được phép sửa giá sản phẩm ở trạng thái ' . Order::getStatusTitle($order->status);
            }
        }



        if(!$order_item || !$order_item instanceof OrderItem){
            $this->action_error[] = 'Sản phẩm #' . $item_id . ' không tồn tại!';
        }

        if(count($this->action_error)){
            return false;
        }

        $old_order_item_price = $order_item->getPriceCalculator();
        $new_order_item_price = (double)$request->get('order_item_price');
        $order_item->price = $new_order_item_price;
        $order_item->price_promotion = $new_order_item_price;
        $order_item->save();

        $order->amount = $order->amountWithItems();
        $order->save();

        if($old_order_item_price <> $new_order_item_price){
            Comment::createComment(
                $user,
                $order_item,
                sprintf("Sửa giá sản phẩm từ %s thành %s", $old_order_item_price, $new_order_item_price),
                Comment::TYPE_NONE,
                Comment::TYPE_CONTEXT_ACTIVITY,
                $order
            );
            $title = "Sửa giá sản phẩm trên đơn ".$order->code;
            $message =   sprintf(" sửa giá sản phẩm từ %s thành %s", $old_order_item_price, $new_order_item_price);
            $content = $user->name. $message;
            CustomerNotification::notificationCustomer($order,$title,$content,'ORDER');
        }

        return true;
    }

    /**
     * @author vanhs
     * @desc Hanh dong mua don hang
     * @param Request $request
     * @param Order $order
     * @param User $user
     * @return bool
     */
    private function __bought_order(Request $request, Order $order, User $user){
        if($order->status != Order::STATUS_DEPOSITED){
            $this->action_error[] = sprintf('Đơn hiện đang ở trạng thái [%s], không thể chuyển sang đã mua!', Order::getStatusTitle($order->status));
        }

        if(!$user->isGod()
            && $user->id != $order->crane_staff_id){
            $this->action_error[] = sprintf('Bạn không có quyền mua đơn hàng này');
            return false;
        }

        if(empty($order->account_purchase_origin)){
            $this->action_error[] = 'Vui lòng chọn user mua hàng site gốc!';
        }

        $exists_original_bill = Order::find($order->id)->original_bill()->count();
        if(!$exists_original_bill){
            $this->action_error[] = 'Vui lòng nhập mã hóa đơn gốc !';
        }

        if($order->domestic_shipping_fee < 0){
            $this->action_error[] = 'Vui lòng nhập vào phí vận chuyển nội địa TQ!';
        }

        if(empty($order->receive_warehouse)){
            $this->action_error[] = 'Vui lòng chọn kho nhận hàng bên Trung Quốc!';
        }

        if(empty($order->destination_warehouse)){
            $this->action_error[] = 'Vui lòng chọn kho phân phối tại Việt Nam!';
        }

        if(count($this->action_error)){
            return false;
        }

        $customer = User::find($order->user_id);
        $order_amount = $order->amountWithItems(true);
        $deposit_percent_new = $order->deposit_percent;
        $deposit_amount_new = Cart::getDepositAmount($deposit_percent_new, $order_amount);
        $deposit_amount_old = UserTransaction::getDepositOrder($customer, $order);
        $deposit_amount_old = abs($deposit_amount_old);

        $order->changeStatus(Order::STATUS_BOUGHT, false);
        $order->paid_staff_id = $user->id;
        $order->deposit_amount = $deposit_amount_new;
        $order->save();

        Comment::createComment($user, $order, "Đơn hàng đã được mua thành công.", Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);
        Comment::createComment($user, $order, "Chuyển trạng thái đơn hàng sang đã mua.", Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

        $title = "Đơn hàng được mua thành công ";
        $message =   " đơn hàng ".$order->code." đã được mua thành công ";
        $content = $user->name. $message;
        CustomerNotification::notificationCustomer($order,$title,$content,'ORDER');


        $user_transaction_amount = 0 - abs($deposit_amount_old - $deposit_amount_new);

        if(abs($user_transaction_amount) <> 0){
            $text = 'truy thu';

            if($deposit_amount_old > $deposit_amount_new){
                $user_transaction_amount = abs($deposit_amount_old - $deposit_amount_new);
                $text = 'trả lại';
            }

            $money = Util::formatNumber(abs($deposit_amount_old - $deposit_amount_new));
            $message = "Hệ thống tiến hành {$text} số tiền {$money} để đảm bảo tỉ lệ đặt cọc {$deposit_percent_new}%";

            Comment::createComment($user, $order, $message, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_LOG);
            Comment::createComment($user, $order, $message, Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_LOG);


            UserTransaction::createTransaction(
                UserTransaction::TRANSACTION_TYPE_DEPOSIT_ADJUSTMENT,
                $message,
                $user,
                $customer,
                $order,
                $user_transaction_amount
            );

            $title = "Tài chính đơn hàng";
            $content = $user->name." ".$message.' trên đơn '.$order->code;
            CustomerNotification::notificationCustomer($order,$title,$content,'ORDER');
        }

        return true;
    }

    /**
     * @author vanhs
     * @desc Huy don hang
     * @param Request $request
     * @param Order $order
     * @param User $user
     * @return bool
     */
    private function __cancel_order(Request $request, Order $order, User $user){
//        if($order->isAfterStatus(Order::STATUS_TRANSPORTING, true)){
//            $this->action_error[] = 'Đơn hàng bắt đầu vận chuyển về Việt Nam. Không thể hủy đơn hàng!';
//            return false;
//        }

        $can_change_order_cancel =
                    ( $order->isBeforeStatus(Order::STATUS_TRANSPORTING) && $order->crane_staff_id == $user->id )
                    || ( $order->isBeforeStatus(Order::STATUS_TRANSPORTING) && $user->isGod() )
                    || ( $order->isBeforeStatus(Order::STATUS_TRANSPORTING) && Permission::isAllow(Permission::PERMISSION_ORDER_BUYING_CAN_SET_CRANE_STAFF) );
        if(!$can_change_order_cancel){
            $this->action_error[] = 'Không thể hủy đơn hàng!';
            return false;
        }

        $order->changeStatus(Order::STATUS_CANCELLED);

        $customer = User::find($order->user_id);

        $deposit_amount = UserTransaction::getDepositOrder($customer, $order);
        if($deposit_amount < 0){
            UserTransaction::createTransaction(
                UserTransaction::TRANSACTION_TYPE_ORDER_REFUND,
                sprintf('Trả lại tiền đặt cọc đơn hàng %s', $order->code),
                $user,
                $customer,
                $order,
                abs($deposit_amount)
            );
        }

        // đặt vị trí

        Comment::createComment($user, $order, "Hủy đơn hàng.", Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);
        Comment::createComment($user, $order, "Hủy đơn hàng.", Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);
        $title = "Trạng thái đơn hàng";
        $content = $user->name . " hủy đơn hàng";
        CustomerNotification::notificationCustomer($order,$title,$content,'ORDER');


        return true;
    }

    /**
     * @author vanhs
     * @desc Chuyen trang thai don sang nhatminh247 nhan hang
     * @param Request $request
     * @param Order $order
     * @param User $user
     * @return bool
     */
    private function __received_from_seller_order(Request $request, Order $order, User $user){
        if($order->status != Order::STATUS_SELLER_DELIVERY){
            $this->action_error[] = sprintf('Không thể chuyển đơn hàng sang trạng thái %s!', Order::getStatusTitle(Order::STATUS_RECEIVED_FROM_SELLER));
        }

        if(count($this->action_error)){
            return false;
        }

        $order->changeOrderReceivedFromSeller(true);

        return true;
    }

    /**
     * @author vanhs
     * @desc Commment san pham
     * @param Request $request
     * @param Order $order
     * @param User $user
     * @return bool
     */
    private function __order_item_comment(Request $request, Order $order, User $user){
        $item_id = $request->get('item_id');
        $order_item = OrderItem::find($item_id);
        return Comment::createComment(
            $user,
            $order_item,
            $request->get('order_item_comment_message'),
            Comment::TYPE_NONE,
            Comment::TYPE_CONTEXT_CHAT,
            $order
        );
    }

    /**
     * @author vanhs
     * @desc Them user mua hang site goc
     * @param Request $request
     * @param Order $order
     * @param User $user
     * @return bool
     */
    private function __account_purchase_origin(Request $request, Order $order, User $user){
        if(!$order->isBeforeStatus(Order::STATUS_BOUGHT)){
            $this->action_error[] = sprintf('Không thể thay đổi acc mua hàng khi đơn ở trạng thái %s', Order::getStatusTitle($order->status));
        }

        if(count($this->action_error)){
            return false;
        }

        $message = null;

        $account_old = $order->account_purchase_origin;
        $account_new = $request->get('select');

        if(empty($account_old)){
            $message = sprintf('Chon user mua hàng site gốc %s', $account_new);
        }else{
            $message = sprintf('Thay đổi user mua hàng site gốc %s -> %s', $account_old, $account_new);
        }

        $order->account_purchase_origin = $account_new;
        $order->save();

        Comment::createComment($user, $order, $message, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

        return true;
    }

    /**
     * @author vanhs
     * @desc Thiet lap kho nhan hang
     * @param Request $request
     * @param Order $order
     * @param User $user
     * @return bool
     */
    private function __receive_warehouse(Request $request, Order $order, User $user){
        $message = null;

        $old = $order->receive_warehouse;
        $new = $request->get('select');

        if(empty($old)){
            $message = sprintf('Thiết lập kho nhận hàng %s', $new);
        }else{
            $message = sprintf('Thay đổi kho nhận hàng %s -> %s', $old, $new);
        }

        $order->receive_warehouse = $new;
        $order->save();

        Comment::createComment($user, $order, $message, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

        return true;
    }

    /**
     * @author vanhs
     * @desc Thiet lap kho phan phoi
     * @param Request $request
     * @param Order $order
     * @param User $user
     * @return bool
     */
    private function __destination_warehouse(Request $request, Order $order, User $user){
        $message = null;

        $old = $order->destination_warehouse;
        $new = $request->get('select');

        if(empty($old)){
            $message = sprintf('Thiết lập kho phân phối %s', $new);
        }else{
            $message = sprintf('Thay đổi kho phân phối %s -> %s', $old, $new);
        }

        $order->destination_warehouse = $new;
        $order->save();

        Comment::createComment($user, $order, $message, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

        return true;
    }

    /**
     * @author vanhs
     * @desc Doi ti le dat coc
     * @param Request $request
     * @param Order $order
     * @param User $user
     * @return bool
     */
    private function __change_deposit(Request $request, Order $order, User $user){

        $old_deposit_percent = $order->deposit_percent;
        $new_deposit_percent = (double)$request->get('deposit');

        if($new_deposit_percent > 100){
            $this->action_error[] = 'Tỉ lệ đặt cọc không hợp lệ!';
        }

        if(!$order->isBeforeStatus(Order::STATUS_BOUGHT)){
            $this->action_error[] = 'Không được phép sửa tỉ lệ đặt cọc đơn ở trạng thái này!';
        }

        if(count($this->action_error)){
            return false;
        }

        $order->deposit_percent = $new_deposit_percent;
        $order->save();

        if($old_deposit_percent <> $new_deposit_percent) {
            $message = sprintf("Thay đổi tỉ lệ đặt cọc đơn hàng từ %s thành %s", $old_deposit_percent, $new_deposit_percent);

            Comment::createComment($user, $order, $message, Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);
            Comment::createComment($user, $order, $message, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

            $title = "Thay đối tỷ lệ đặt cọc trên đơn ";
            $content = $user->name." ".$message." trên đơn ".$order->code;
            CustomerNotification::notificationCustomer($order,$title,$content,'ORDER');
        }
        return true;
    }

    /**
     * @author vanhs
     * @desc Phi van chuyen noi dia TQ
     * @param Request $request
     * @param Order $order
     * @param User $user
     * @return bool
     */
    private function __domestic_shipping_china(Request $request, Order $order, User $user)
    {
        if($user->isGod()){

        }else{
            if(!$order->isBeforeStatus(Order::STATUS_BOUGHT)){
                $this->action_error[] = sprintf('Không thể thay đổi phí vận chuyển nội địa TQ khi đơn ở trạng thái %s', Order::getStatusTitle($order->status));
            }
        }



        if(count($this->action_error)){
            return false;
        }

        $domestic_shipping_fee = $request->get('domestic_shipping_china');
        $domestic_shipping_fee_vnd = $domestic_shipping_fee * $order->exchange_rate;

        $order->domestic_shipping_fee = $domestic_shipping_fee;
        $order->domestic_shipping_fee_vnd = $domestic_shipping_fee_vnd;
        $order->save();

        Comment::createComment($user, $order, sprintf('Cập nhật phí vận chuyển nội địa TQ %s ¥', $domestic_shipping_fee), Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);
        Comment::createComment($user, $order, sprintf('Cập nhật phí vận chuyển nội địa TQ %s ¥', $domestic_shipping_fee), Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

        $title = "Phí vận chuyển nội địa";
        $message = sprintf(' cập nhật phí vận chuyển nội địa TQ %s ¥', $domestic_shipping_fee);
        $content = $user->name . $message;
        CustomerNotification::notificationCustomer($order,$title,$content,'ORDER');

        return true;
    }


}
