<?php
/**
 * Created by PhpStorm.
 * User: goerge
 * Date: 15/04/2017
 * Time: 11:57
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Complaints extends Model
{
    protected $table = 'complaints';

    // constant status
    const STATUS_CREATE = 'CREATE'; // khoi tao
    const STATUS_ACCEPT = 'ACCEPT'; // chap nhan
    const STATUS_FINISH = 'FINISH'; // hoan thanh
    const STATUS_REJECT = 'REJECT'; // tu cho

    public static $alias_array = [
        self::STATUS_CREATE => 'Khởi tạo',
        self::STATUS_ACCEPT => 'Tiếp nhận và xử lý',
        self::STATUS_FINISH => 'Hoàn thành',
        self::STATUS_REJECT => 'Từ chối'
    ];
    
    /**
     * create new complaint
     * @param array $complaint
     * @return bool
     */
    public function createComplaint($complaint = array()){

        $result  = $this->insertGetId([
            'order_id' => $complaint['order_id'],
            'customer_id' => $complaint['customer_id'],
            'description' => $complaint['comment'],
            'status' => $complaint['status'], // trang thai la khoi tao
            'title' => $complaint['title'],
            'created_time' =>   date('Y-m-d H:i:s',time())
        ]);

        if(!$result){
            return false;
        }
        return $result;
    }

    /**
     * get order_code by order_id
     * @param $order_id
     * @return mixed|string
     */
   public static function getOrderCode($order_id){
       $order = new Order();
       $order_code = $order->findOneByIdOrCode($order_id);
       if($order_code instanceof Order){
           return $order_code->code;
       }
       return '';
   }

    /**
     * ham tra lai gia user nam khi truyen vao id khach
     * @param $customer_id
     * @return mixed|string
     */
    public static function getCustomerUsername($customer_id){
        $customer = User::where('id',$customer_id)->first();
        if($customer instanceof User){
            return $customer->name;
        }
        return '';
    }
}