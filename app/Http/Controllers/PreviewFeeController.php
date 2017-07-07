<?php

namespace App\Http\Controllers;

use App\Exchange;
use App\Library\ServiceFee\ServiceFactoryMethod;
use App\Location;
use App\Service;
use App\User;
use App\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class PreviewFeeController extends Controller
{
    protected $message = [];

    function __construct()
    {

    }

    public function index(){
        $exchange_rate = Exchange::getExchange();

        $data = Input::all();

        $all_provinces = Location::getAllProvinces();
        $all_districts = Location::getAllDistricts();
        $all_type_amount = [
            'vnd' => 'Việt Nam Đồng',
            'ndt' => 'Nhân Dân Tệ'
        ];

        $all_type_domestic_shipping_china = [
            'ndt' => 'Nhân Dân Tệ',
            'vnd' => 'Việt Nam Đồng',
        ];

        $return_data = null;
        $is_submit = isset($data['is_submit']);
        if($is_submit){
            $return_data = $this->__calculatorFee($data);
        }

        return view('preview_fee', [
            'page_title' => 'Công cụ tính phí',
            'all_districts' => $all_districts,
            'all_provinces' => $all_provinces,
            'exchange_rate' => $exchange_rate,
            'data' => $data,
            'all_type_amount' => $all_type_amount,
            'all_type_domestic_shipping_china' => $all_type_domestic_shipping_china,
            'return_data' => $return_data,
        ]);
    }

    private function __calculatorFee($data){

        $exchange_rate = Exchange::getExchange();

        $data['amount'] = doubleval($data['amount']);

        if($data['type_amount'] == 'ndt'){
            $data['amount'] = $data['amount'] * $exchange_rate;
        }

        if($data['type_domestic_shipping_china'] == 'ndt'){
            $data['domestic_shipping_china'] = $data['domestic_shipping_china'] * $exchange_rate;
        }

        $weight_calculator = 0;
        $weight = doubleval($data['weight']);
        $converted_weight = ($data['length_package'] * $data['width_package'] * $data['height_package']) / 6000;
        $converted_weight = doubleval($converted_weight);

        if($data['type_weight'] == 1){
            $weight_calculator = $weight;
        }else if($data['type_weight'] == 2){
            $weight_calculator = $converted_weight > $weight ? $converted_weight : $weight;
        }

        $factoryMethodInstance = new ServiceFactoryMethod();
        //============phi mua hang===========
        $service = $factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_BUYING,
            'total_amount' => $data['amount'],
            'apply_time' => date('Y-m-d H:i:s')
        ]);
        $buying_fee = doubleval($service->calculatorFee());

        //============phi van chuyen TQ-VN===========
        $destination_warehouse = User::getDestinationWarehouseWithLocation($data['province_id'], $data['district_id']);

        //-- Can nang truyen vao phai chuyen sang kg
        $service = $factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_SHIPPING_CHINA_VIETNAM,
            'weight' => $weight_calculator,
            'destination_warehouse' => $destination_warehouse,
            'apply_time' => date('Y-m-d H:i:s')
        ]);
        $shipping_china_vietnam = doubleval($service->calculatorFee());

        $total_fee = $data['amount'] + $buying_fee
            + $data['domestic_shipping_china'] + $shipping_china_vietnam;

        return [
            'buying_fee' => $buying_fee,
            'shipping_china_vietnam' => $shipping_china_vietnam,
            'converted_weight' => $converted_weight,
            'total_fee' => $total_fee,
        ];

    }
}
