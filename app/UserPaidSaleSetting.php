<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserPaidSaleSetting extends Model
{
    protected $table = 'user_paid_sale_setting';

    /**
     * @author vanhs
     * @desc Lay ra tong tien bao khach (tien hang + ship noi dia TQ) theo nguoi mua hang va thoi gian
     * @param $crane_paid
     * @param $start_time
     * @param $end_time
     * @param $field_name
     * @return int
     */
    public static function getOrderAmountWithCranePaidAndMonth($crane_paid, $field_name, $start_time, $end_time){
        $money = 0;
        $sql_select = " sum({$field_name}) as money ";
        $sql = "
            select {$sql_select} from `order` where 
            `status` not in ('CANCELLED') 
            and paid_staff_id = {$crane_paid}
            and bought_at >= '{$start_time}' 
            and bought_at <= '{$end_time}';
        ";
        $query = DB::select($sql);
        if($query){
            return $query[0]->money;
        }
        return $money;
    }

    /**
     * @author vanhs
     * @desc Lay cau hinh luong cua nhan vien mua hang theo tung thang
     * @param $crane_paid_staff_id
     * @param $month
     * @return null
     */
    public static function getSettingWithCranePaidId($crane_paid_staff_id, $month){
        $sql = "
        select * from `user_paid_sale_setting` 
        where paid_user_id = {$crane_paid_staff_id} 
            and '{$month}' >= activated_at 
            and '{$month}' <= deadlined_at 
        limit 1;
        ";
        $query = DB::select($sql);
        if($query){
            return $query[0];
        }
        return null;
    }

    /**
     * @author vanhs
     * @desc Lay phan tram thuc tinh doanh so cho nhan vien mua hang theo tung thang
     * @param $crane_id
     * @param $buying_month
     * @return int
     */
    public static function getPercentRealCalValueWithCraneAndBuyingMonth($crane_id, $buying_month){
        $percent = 0;
        $setting_in_month = UserPaidSaleSetting::getSettingWithCranePaidId($crane_id, $buying_month);
        if($setting_in_month){
            $year = date('Y', strtotime($buying_month));
            $month = date('m', strtotime($buying_month));

            $start_month = sprintf("%s-%s-01 00:00:00", $year, $month);
            $end_month = sprintf("%s-%s-%s 23:59:59", $year, $month, cal_days_in_month(CAL_GREGORIAN, $month, $year));

            //tong tien bao khach trong thang
            $amount_customer_current_month_vnd = UserPaidSaleSetting::getOrderAmountWithCranePaidAndMonth(
                $crane_id,
                'customer_amount_vnd',
                $start_month,
                $end_month
            );
            //tong tien thuc mua trong thang
            $amount_original_current_month_vnd = UserPaidSaleSetting::getOrderAmountWithCranePaidAndMonth(
                $crane_id,
                'amount_original_vnd',
                $start_month,
                $end_month
            );

            $amount_bargain_current_month_vnd = $amount_customer_current_month_vnd - $amount_original_current_month_vnd;

            //phan tram mac ca trong thang
            $percent_bargain_current_month = 0;
            if($amount_customer_current_month_vnd){
                $percent_bargain_current_month = $amount_bargain_current_month_vnd * 100
                    / $amount_customer_current_month_vnd;
            }


            //dat chi tieu mac ca trong thang hay khong?
            $is_bargain_target = false;
            if($percent_bargain_current_month >= $setting_in_month->require_min_bargain_percent){
                $is_bargain_target = true;
            }

            //phan tram thuc tinh doanh so
            $percent = $is_bargain_target
                ? $setting_in_month->rose_percent : $setting_in_month->rose_percent_min;

        }
        return $percent;
    }
}
