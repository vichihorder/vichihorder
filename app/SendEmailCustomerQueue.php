<?php
/**
 * Created by PhpStorm.
 * User: goerge
 * Date: 27/05/2017
 * Time: 10:28
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class SendEmailCustomerQueue extends Model
{
    protected $table = 'send_email_customer_queue';

    const NOT_YET = 'NOT_YET';// chưa gửi t
    const SUCCESS = 'SUCCESS'; // gưi thành cong
    const FAIL = 'FAIL'; // guiwr that bai

    /**
     * luwu vafo mail khi quets nhap
     * @param $data
     */
    public function EmailQueueOrder($data){
        $this->order_id = $data['order_id'];
        $this->email = $data['email'];
        $this->user_id = $data['user_id'];
        $this->content = $data['content'];
        $this->send_status = $data['send_status'];
        $this->save();
    }

    /**
     * @param $user
     * @param $content
     */
    public function EmailQueueWhenCreateTransactionAdjustment(User $user, $content){
        $this->email = $user->email;
        $this->user_id = $user->id;
        $this->content = $content;
        $this->send_status = self::NOT_YET;
        $this->save();
    }

}