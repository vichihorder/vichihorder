<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageService extends Model
{
    protected $table = 'package_service';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_DISABLED = 'DISABLED';

    const TYPE_WOOD_CRATING = 'WOOD_CRATING';

    public static $service_list = [
        self::TYPE_WOOD_CRATING => 'Đóng gỗ'
    ];

    public static $service_using = [
        self::TYPE_WOOD_CRATING
    ];

    public static function getServiceName($service_code){
        if(!empty(self::$service_list[$service_code])){
            return self::$service_list[$service_code];
        }
        return '';
    }

    public static function getServiceList(){
        return self::$service_list;
    }

    public static function insertService(Package $package, $service_code = null){
        if(!$service_code
            || !$package instanceof Package){
            return false;
        }

        $package_service = PackageService::where([
            'package_id' => $package->id
        ])->first();

        if($package_service instanceof PackageService){
            $package_service->status = self::STATUS_ACTIVE;
            $package_service->save();
        }else{
            $order_id = null;
            $order_code = null;
            $order = Order::find($package->order_id);
            if($order instanceof Order){
                $order_id = $order->id;
                $order_code = $order->code;
            }

            return self::insert([
                'package_id' => $package->id,
                'logistic_package_barcode' => $package->logistic_package_barcode,
                'order_id' => $order_id,
                'order_code' => $order_code,
                'code' => $service_code
            ]);
        }
    }

    public static function removeService(Package $package, $service_code = null){
        if(!$service_code
            || !$package instanceof Package){
            return false;
        }


        return PackageService::where([
            'package_id' => $package->id,
            'code' => $service_code
        ])->update([
            'status' => self::STATUS_DISABLED,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
