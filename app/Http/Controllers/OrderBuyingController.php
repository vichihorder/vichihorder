<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Order;
use App\OrderFee;
use App\OrderService;
use App\Permission;
use App\Service;
use App\User;
use App\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;

class OrderBuyingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexs(Request $request){
        $status_list = [];

        $status_data = [
            Order::STATUS_DEPOSITED => 'Đã đặt cọc',
            Order::STATUS_BOUGHT => 'Đã mua hàng',
            Order::STATUS_CANCELLED => "Đã hủy",
        ];

        foreach($status_data as $key => $val){
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

        return view('orders_buying', [
            'page_title' => ' Mua hàng',
            'status_list' => $status_list,
        ]);

    }

    /**
     * @author vanhs
     * @desc Gan/bo gan don hang cho nhan vien mua hang
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setCraneStaff(Request $request){
        $current_user = User::find(Auth::user()->id);

        $can_set_crane_buying = Permission::isAllow(Permission::PERMISSION_ORDER_BUYING_CAN_SET_CRANE_STAFF)
            || $current_user->isGod();

        if(!$can_set_crane_buying){
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này!']);
        }

        $order = Order::find($request->get('order_id'));
        if(!$order instanceof Order){
            return response()->json(['success' => false, 'message' => 'Không tồn tại đơn hàng!']);
        }

        $user_id = $request->get('user_id');
        if($user_id){
            $user = User::find($user_id);
            if(!$user instanceof User){
                return response()->json(['success' => false, 'message' => 'Không tồn tại nhân viên!']);
            }
        }


        if(!$user_id){
            $order->crane_staff_id = null;
            $order->crane_staff_at = null;
        }else{
            $order->crane_staff_id = $user_id;
            $order->crane_staff_at = date('Y-m-d H:i:s');

            $message = sprintf("Phân đơn hàng cho nhân viên %s (%s)", $user->email, $user->code);
            Comment::createComment($current_user, $order, $message, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);
        }
        $order->save();

        return response()->json(['success' => true, 'message' => '']);
    }

    /**
     * @author vanhs
     * @desc Danh sach don mua hang (gianh cho nhan vien mua hang)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrdersData(){
        $per_page = 20;

        $params = Input::all();

        $orders = Order::select('*');
        $orders = $orders->orderBy('id', 'desc');

        if(!empty($params['order_code'])){
            $orders = $orders->where('code', $params['order_code']);
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

        $orders->where('crane_staff_id', Auth::user()->id);

        $total_orders = $orders->count();
        $orders = $orders->paginate($per_page);
        $orders->withPath('order_buying');

        $order_ids = [];
        if($total_orders){
            foreach($orders as $order){
                if(!$order instanceof Order){
                    continue;
                }

                $order_ids[] = $order->id;

                $order->paid_staff = null;
                if($order->paid_staff_id){
                    $order->paid_staff = User::find($order->paid_staff_id);
                }

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

        $view = View::make('orders_data', [
            'total_orders' => $total_orders,
            'orders' => $orders,
            'can_set_crane_buying' => false
        ]);
        $html = $view->render();

        return response()->json([
            'html' => $html,
            'success' => true,
            'message' => null
        ]);
    }
}
