<?php
/**
 * Created by PhpStorm.
 * User: goerge
 * Date: 13/04/2017
 * Time: 21:36
 */

namespace App;


use Illuminate\Database\Eloquent\Model;


class CustomerNotification extends Model
{
    protected $table = 'customer_notification';

    const CUSTOMER_NOTIFICATION_READ = 'READ';
    const CUSTOMER_NOTIFICATION_VIEW = 'VIEW';
    const CUSTOMER_NOTIFICATION_IS_READ = 'IS_READ'; // ddax ddoc

    public function createNewNotification($user_id , $order_id , $array_data){

        $result  = $this->insert([
            'order_id' => $order_id,
            'user_id' => $user_id,
            'notification_content' => $array_data['notification_content'],
            'type' => $array_data['type'],
            'is_view' => $array_data['is_view'],
            'section' => $array_data['section'],
            'created_time' =>   date('Y-m-d H:i:s',time())
        ]);

        if(!$result){
            return false;
        }
        return true;

    }

    /**
     * @param Order $order
     * @param $title_notification
     * @param $notification_content
     * @return bool
     */
    public static function notificationCustomer($order,$title_notification,$notification_content,$type){
        $result  = self::insert([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'notification_content' => $notification_content,
            'title' => $title_notification,
            'type' => $type,
            'is_view' => self::CUSTOMER_NOTIFICATION_VIEW,
            'section' => User::SECTION_CUSTOMER,
            'created_time' =>   date('Y-m-d H:i:s',time())
        ]);

        if(!$result){
            return false;
        }
        return true;
    }

    /**
     * @param Order $order
     * @param $title_notification
     * @param $notification_content
     * @param $type
     * @return bool
     */
    public static function notificationCrane($order,$title_notification,$notification_content,$type){
        $result  = self::insert([
            'order_id' => $order->id,
            'user_id' => $order->paid_staff_id,
            'title' => $title_notification,
            'notification_content' => $notification_content,
            'type' => $type,
            'is_view' => self::CUSTOMER_NOTIFICATION_VIEW ,
            'section' =>  User::SECTION_CRANE ,
            'created_time' =>   date('Y-m-d H:i:s',time())
        ]);

        if(!$result){
            return false;
        }
        return true;
    }

}