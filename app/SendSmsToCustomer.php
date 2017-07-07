<?php
/**
 * Created by PhpStorm.
 * User: goerge
 * Date: 25/05/2017
 * Time: 13:42
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class SendSmsToCustomer extends Model
{
    protected $table = 'send_sms_to_customer';

    const NOT_YET = 'NOT_YET';// chưa gửi t
    const SUCCESS = 'SUCCESS'; // gưi thành cong
    const FAIL = 'FAIL'; // guiwr that bai

    /**
     * save giá trị 
     * @param $data
     */
    public function CustomerSms($data){
        $this->order_id = $data['order_id'];
        $this->phone = $data['phone'];
        $this->content = $data['content'];
        $this->user_id = $data['user_id'];
        $this->send_status = self::NOT_YET;
        $this->save();
    }

    /**
     * lưu lại khi nạp tiền
     * @param User $user
     * @param $content
     */
    public function sendSmsWhenCreateTransaction(User $user,$content){
        $user_mobiles = UserMobile::where('user_id',$user->id)->first();
        $mobile = "01649647164";

//        if(count($user_mobiles) > 0){
//            foreach ($user_mobiles as $item_mobiles){
//                if($item_mobiles->verify_times == 1){
//                    $mobile = $item_mobiles->mobile;
//                    break;
//                }
//            }
//        }
        if($user_mobiles instanceof UserMobile){
            $mobile = $user_mobiles->mobile;
        }
        $this->order_id = 1;
        $this->phone =  $mobile;
        $this->content = $content;
        $this->user_id = $user->id;
        $this->send_status = self::NOT_YET;
        $this->save();
    }
}