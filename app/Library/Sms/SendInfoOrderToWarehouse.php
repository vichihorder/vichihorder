<?php
/**
 * Created by PhpStorm.
 * User: goerge
 * Date: 25/05/2017
 * Time: 16:32
 */

namespace App\Library\Sms;




class SendInfoOrderToWarehouse
{
    public function sendSms($phones, $content){
        $sms = new SpeedSMSAPI();
        $result = $sms->sendSMS($phones, $content, SpeedSMSAPI::SMS_TYPE_CSKH, "");
        return  $result;
    }

    

    

}