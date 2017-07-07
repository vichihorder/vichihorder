<?php

namespace App\Http\Controllers;

use App\BillManage;
use App\Comment;
use App\Location;
use App\Order;
use App\Package;
use App\Permission;
use App\User;
use App\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillManageController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    protected $_per_page = 20;

    /**
     * @param Request $request
     * @author vanhs
     * @desc danh sach phieu giao hang
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function listView(Request $request){
        $can_view = Permission::isAllow(Permission::PERMISSION_BILL_MANAGE_LIST_VIEW);
        if(!$can_view){
            return redirect('403');
        }


        $bill_mange = BillManage::where([]);
        $total_bill_manage = $bill_mange->count();

        $bill_mange = $bill_mange->orderBy('id', 'desc');

        $bill_mange = $bill_mange->paginate($this->_per_page);

        $bill_mange_list = [];

        if($bill_mange){
            foreach($bill_mange as $bill_mange_item){
                if(!$bill_mange_item instanceof BillManage){
                    continue;
                }

                $bill_mange_item->create_user_object = null;
                $bill_mange_item->buyer_object = null;

                $create_user = User::find($bill_mange_item->create_user);
                if($create_user instanceof User){
                    $bill_mange_item->create_user_object = $create_user;
                }

                $buyer = User::find($bill_mange_item->buyer_id);
                if($buyer instanceof User){
                    $bill_mange_item->buyer_object = $buyer;
                }

                $orders_array = explode(',', $bill_mange_item->orders);
                $orders_temp = [];
                foreach($orders_array as $orders_array_item){
                    $orders_temp[] = sprintf("<a href='%s'>%s</a>", url('order/detail', $orders_array_item), $orders_array_item);
                }
                $bill_mange_item->orders_links = implode(', ', $orders_temp);


                $packages_array = explode(',', $bill_mange_item->packages);
                $packages_temp = [];
                foreach($packages_array as $packages_array_item){
                    $packages_temp[] = sprintf("<a href='%s'>%s</a>", url('package', $packages_array_item), $packages_array_item);
                }
                $bill_mange_item->packages_links = implode(', ', $packages_temp);

                $bill_mange_list[] = $bill_mange_item;
            }
        }

        return view('billManage', [
            'page_title' => 'Phiếu giao hàng',
            'bill_mange_list' => $bill_mange_list,
            'bill_mange' => $bill_mange,
            'total_bill_manage' => $total_bill_manage
        ]);
    }

    /**
     * @author vanhs
     * @desc Xu ly tao phieu giao:
     *  - xuat toan bo kien (da chon) trong kien
     *  - tao thong tin phieu
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request){
        $can_create = Permission::isAllow(Permission::PERMISSION_BILL_MANAGE_CREATE);
        if(!$can_create){
            return response()->json(['success' => false, 'message' => 'Ban khong co quyen thuc hien hanh dong nay!']);
        }

        try{
            DB::beginTransaction();

            $create_user = User::find(Auth::user()->id);
            $packages = $request->get('packages');
            $package_string = implode(',', $packages);
            $orders = $request->get('orders');
            $order_array = [];
            foreach($orders as $order_id){
                $order_array[$order_id] = $order_id;
            }
            $order_string = implode(',', array_values($order_array));

            foreach($packages as $logistic_package_barcode){
                $package = Package::where([
                    ['logistic_package_barcode', '=', $logistic_package_barcode]
                ])->first();

                $message_internal = sprintf("Kiện hàng %s xuất kho phân phối %s",
                    $logistic_package_barcode,
                    $package->current_warehouse);

                if($package instanceof Package){
                    $package->outputWarehouseDistribution($package->current_warehouse);
                    $order = Order::find($package->order_id);
                    if($order instanceof Order){
                        $order->changeOrderDelivering();
                        Comment::createComment($create_user, $order, $message_internal, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);
                    }
                }
            }

            $id = BillManage::insertGetId(
                [
                    'create_user' => Auth::user()->id,
                    'code' => BillManage::getCode(),
                    'domestic_shipping_vietnam' => $request->get('domestic_shipping_vietnam'),
                    'amount_cod' => $request->get('amount_cod'),
                    'packages' => $package_string,
                    'orders' => $order_string,
                    'buyer_id' => $request->get('buyer_id'),
                    'buyer_address_id' => $request->get('buyer_address_id'),
                    'created_at' => date('Y-m-d H:i:s')
                ]
            );
            $url = url('BillManage/Detail', $id);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'thanh cong', 'url' => $url]);

        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'co loi xay ra, vui long thu lai' . $e->getMessage()]);
        }

    }

    /**
     * @author vanhs
     * @desc cap nhat phi van chuyen noi dia vn, tien thu ho tren chi tiet phieu giao hang
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFee(Request $request){
        $bill = BillManage::find($request->get('bill_id'));
        if(!$bill instanceof BillManage){
            return response()->json(['success' => false, 'message' => 'phieu khong ton tai']);
        }

        $type = $request->get('type');
        if($type == 'amount_cod'){

        }else if($type == 'domestic_shipping_vietnam'){

        }
        $bill->$type = $request->get('amount');
        $bill->save();

        return response()->json(['success' => true, 'message' => '']);
    }

    /**
     * @author vanhs
     * @desc in phieu giao hang
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function printBill(Request $request){
        $bill_id = $request->route('id');
        $bill = BillManage::find($bill_id);
        if(!$bill instanceof BillManage){
            return redirect('404');
        }

        $bill = $this->__init_data_detail_view($bill);

        return view('printBill', [
            'page_title' => 'In phiếu giao',
            'bill' => $bill
        ]);
    }

    private function __init_data_detail_view(BillManage $bill){
        $bill->create_user_object = null;
        $bill->buyer_object = null;

        $create_user = User::find($bill->create_user);
        if($create_user instanceof User){
            $bill->create_user_object = $create_user;
        }

        $buyer = User::find($bill->buyer_address_id);
        if($buyer instanceof User){
            $bill->buyer_object = $buyer;
        }

        $orders_array = explode(',', $bill->orders);
        $orders_temp = [];
        foreach($orders_array as $orders_array_item){
            $orders_temp[] = sprintf("<a href='%s'>%s</a>", url('order/detail', $orders_array_item), $orders_array_item);
        }
        $bill->orders_links = implode(', ', $orders_temp);


        $packages_array = explode(',', $bill->packages);
        $packages_temp = [];
        $packages = [];
        foreach($packages_array as $packages_array_item){
            $p = Package::where([
                ['logistic_package_barcode', '=', $packages_array_item]
            ])->first();
            $p->order = Order::find($p->order_id);

            $packages[] = $p;
            $packages_temp[] = sprintf("<a href='%s'>%s</a>", url('package', $packages_array_item), $packages_array_item);
        }
        $bill->packages_links = implode(', ', $packages_temp);

        $bill->buyer_address = UserAddress::find($bill->buyer_address_id);
        $bill->buyer_address->district = null;
        $bill->buyer_address->province = null;

        $district = Location::where([
            ['type', '=', Location::TYPE_DISTRICT],
            ['id', '=', $bill->buyer_address->district_id]
        ])->first();
        if($district instanceof Location){
            $bill->buyer_address->district = $district;
        }

        $province = Location::where([
            ['type', '=', Location::TYPE_STATE],
            ['id', '=', $bill->buyer_address->province_id]
        ])->first();
        if($province instanceof Location){
            $bill->buyer_address->province = $province;
        }

        $bill->packages = $packages;


        return $bill;
    }

    /**
     * @author vanhs
     * @desc Chi tiet phieu giao hang
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function detailView(Request $request){
        $id = $request->route('id');
        $bill = BillManage::find($id);

        $can_view = Permission::isAllow(Permission::PERMISSION_BILL_MANAGE_LIST_VIEW);
        if(!$can_view){
            return redirect('403');
        }

        if(!$bill instanceof BillManage){
            return redirect('404');
        }

        $bill = $this->__init_data_detail_view($bill);

        return view('billDetail', [
            'page_title' => sprintf("Phiếu giao #%s", $bill->code),
            'bill' => $bill
        ]);
    }
}
