<?php

namespace App\Http\Controllers;

use App\Comment;
use App\BillManage;
use App\Location;
use App\Order;
use App\Package;
use App\Permission;
use App\User;
use App\UserAddress;
use App\WareHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryManageController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @author vanhs
     * @desc trang tao phieu giao hang
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function createView(Request $request){
        $can_view = Permission::isAllow(Permission::PERMISSION_DELIVERY_MANAGE_LIST_VIEW);
        if(!$can_view){
            return redirect('403');
        }

        $user_id = $request->get('user_id');
        $user_address_id = $request->get('user_address_id');

        if(!$user_id && !$user_address_id){
            return redirect('404');
        }

        $customer = User::find($user_id);

        $packages_list = [];
        $packages = DB::select("
        select * from packages where 
        `status` in ('WAITING_DELIVERY') and
        order_id in (select id from `order`
        where `status` in ('WAITING_DELIVERY', 'DELIVERING', 'RECEIVED') 
        and user_id = ".$user_id." and user_address_id = ".$user_address_id.");
        ");

        $total_orders = 0;

        $array_order_count_temp = [];
        if($packages){
            foreach($packages as $package){
                $package->order = Order::find($package->order_id);
                $array_order_count_temp[$package->order_id] = $package->order_id;
                $packages_list[] = $package;
            }
        }

        $total_orders = count($array_order_count_temp);

        $user_address_detail = UserAddress::find($user_address_id);

        $user_address_detail->district = null;
        $user_address_detail->province = null;

        $district = Location::where([
            ['type', '=', Location::TYPE_DISTRICT],
            ['id', '=', $user_address_detail->district_id]
        ])->first();
        if($district instanceof Location){
            $user_address_detail->district = $district;
        }

        $province = Location::where([
            ['type', '=', Location::TYPE_STATE],
            ['id', '=', $user_address_detail->province_id]
        ])->first();
        if($province instanceof Location){
            $user_address_detail->province = $province;
        }

        return view('deliveryCreate', [
            'page_title' => 'Tạo phiếu giao',
            'packages_list' => $packages_list,
            'user_address_detail' => $user_address_detail,
            'total_orders' => $total_orders,
            'customer' => $customer
        ]);
    }

    /**
     * @author vanhs
     * @desc danh sach phieu giao hang
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function listView(Request $request){

        $can_view = Permission::isAllow(Permission::PERMISSION_DELIVERY_MANAGE_LIST_VIEW);
        if(!$can_view){
            return redirect('403');
        }

        //Lay danh sach khach hang co hang trong kho de chuan bi giao
        $customer_ids = [];

        $customers = [];
        $customers_list = DB::table('packages')->select(DB::raw('distinct(buyer_id)'))
        ->whereIn('status', [Package::STATUS_WAITING_FOR_DELIVERY])
        ->get();

        if($customers_list){
            foreach($customers_list as $customers_list_item){
                $customer_object = User::find($customers_list_item->buyer_id);
                $customers[] = $customer_object;
                $customer_ids[] = $customer_object->id;
            }
        }

        //Lay danh sach dia chi nhan hang cua toan bo khach hang phia tren
        $customer_user_address = [];
        $user_address_list = DB::table('user_address')->where([

        ])
        ->whereIn('user_id', $customer_ids)
        ->get();

        if($user_address_list){
            foreach($user_address_list as $user_address_list_item){
                $user_address_list_item->district = null;
                $user_address_list_item->province = null;

                $district = Location::where([
                    ['type', '=', Location::TYPE_DISTRICT],
                    ['id', '=', $user_address_list_item->district_id]
                ])->first();
                if($district instanceof Location){
                    $user_address_list_item->district = $district;
                }

                $province = Location::where([
                    ['type', '=', Location::TYPE_STATE],
                    ['id', '=', $user_address_list_item->province_id]
                ])->first();
                if($province instanceof Location){
                    $user_address_list_item->province = $province;
                }


                $query = DB::select("

                select count(id) as total
                from packages
                where
                  `status` in ('WAITING_DELIVERY') and
                   order_id in
                    (select id
                      from `order`
                      where `status` in ('WAITING_DELIVERY', 'DELIVERING', 'RECEIVED')
                      and user_id = ".$user_address_list_item->user_id." and user_address_id = ".$user_address_list_item->id.");

                ");

                $total_package_waiting_delivery = (int)$query[0]->total;
                if($total_package_waiting_delivery){
                    $user_address_list_item->total_package_waiting_delivery = $total_package_waiting_delivery;
                    $customer_user_address[$user_address_list_item->user_id][] = $user_address_list_item;
                }

            }
        }

        return view('deliveryManage', [
            'page_title' => 'Yêu cầu giao hàng',
            'customers' => $customers,
            'customer_user_address' => $customer_user_address
        ]);
    }
}
