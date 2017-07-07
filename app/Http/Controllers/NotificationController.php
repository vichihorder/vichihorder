<?php
/**
 * Created by PhpStorm.
 * User: goerge
 * Date: 25/04/2017
 * Time: 18:53
 */

namespace App\Http\Controllers;


use App\CustomerNotification;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index(){
        $per_page = 20;
        $page = Input::get('page');

        if(!$page || $page == 1){
            $page = 0;
        }else{
            $page = $page - 1;
        }

        $data_notification = DB::table('customer_notification')
            ->where('user_id', '=', Auth::user()->id)
            ->orderBy('id', 'desc')->paginate($per_page);
        if(count($data_notification) < 1){
            $data_notification = [];
        }
        return view('notification',[
            'data' => $data_notification,
            'page_title' => 'notification',
            'per_page' => $per_page,
            'page' => $page
        ]);
    }

    /**
     * @author : giangnh
     * change value to VIEW -> READ
     */
    public function changeStatus(){
        //update value to view to read
        $user_id = Auth::user()->id;
        $section = Auth::user()->section;
        if($section == User::SECTION_CRANE){
            $user_section = User::SECTION_CRANE;
        }else{
            $user_section = User::SECTION_CUSTOMER;
        }
        $count_notification = CustomerNotification::where([
            'section' => $user_section,
            'is_view' => CustomerNotification::CUSTOMER_NOTIFICATION_VIEW,
            'user_id' => $user_id
        ])->update([
            'is_view' => CustomerNotification::CUSTOMER_NOTIFICATION_READ
        ]);

        $response = array(
            'status' => 'success',
            'data' => $count_notification
        );

        return response()->json($response);

    }

    /**
     * khi click vào thông báo thì tắt hiển thị
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeTypeNotification(){
        $id_notification = Input::get('notification_id');

        $notification = CustomerNotification::where([
            'id' => $id_notification
        ])->update([
            'is_view' => CustomerNotification::CUSTOMER_NOTIFICATION_IS_READ
        ]);
        if(!$notification){
            $response = [
                'status' => 'error'
            ];
            return response()->json($response);
        }else{
            $response = [
                'status' => 'success'
            ];
            return response()->json($response);
        }

    }

}