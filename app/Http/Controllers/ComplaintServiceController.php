<?php
/**
 * Created by PhpStorm.
 * User: goerge
 * Date: 18/04/2017
 * Time: 09:52
 */

namespace App\Http\Controllers;


use App\ComplaintFiles;
use App\Complaints;
use App\Order;
use App\User;
use Hamcrest\Core\IsNot;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rules\In;
use Illuminate\Http\Request;

class ComplaintServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * hien thi tren trang cua danh sach
     */
    public function index(Request $request){

        $user_code = Input::get('username');
        $orderCode = Input::get('ordercode');
        $statusOrder = Input::get('status_complaint');

        $complaint_service = Complaints::select('*')->orderBy('id', 'desc');
        // ma khach
        if($user_code){
            $user = User::where('code','=',$user_code)->first();
            if($user instanceof User){
                $user_id = $user->id;
                $complaint_service = $complaint_service->where('customer_id','=',$user_id);
            }
        }
        // ma code cua khach
        if($orderCode){
            $orders = Order::where('code','like','%' . $orderCode . '%')->get();
            $order_ids = [];
            if(count($orders) > 0){
                foreach ($orders as $order){
                    $order_ids[] = $order->id;
                }
            }
            $complaint_service = $complaint_service->whereIn('order_id',$order_ids);

        }
        if($statusOrder){
            if(array_key_exists($statusOrder, Complaints::$alias_array)){
                $complaint_service = $complaint_service->where('status',$statusOrder);
            }
        }
        // trang thái của khiếu nại

        $complaint_service = $complaint_service->paginate();

        if(count($complaint_service) > 0){
            $data = $complaint_service;
        }else{
            $data = [];
        }
        $complaint_status = Complaints::$alias_array;

        return view('complaint_list',[
            'data' => $data,
            'complaint_status' => $complaint_status,
            'page_title' => 'Danh sách khiếu nại'
        ]);

    }


    public function complaintDetail( Request $request ){

        $complaint_id = $request->route('complaint_id');

        $list = Complaints::where(['id' => $complaint_id])->first();

        if($list instanceof  Complaints){
            $complaint = ComplaintFiles::where(['complaint_id' => $complaint_id])->get();
            $data_complaint = [];
            if (count($complaint) > 0){
                $data_complaint = $complaint;
            }
            // xu ly du lieu tra ve
            return view('complaint_detail',[
                'data_complaint' => $list,
                'data_complaint_file' => $data_complaint,
                'page_title' => 'Khiếu nại'
            ]);
        }else{
            return redirect('404');
        }
    }

}