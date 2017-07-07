<?php

namespace App\Library\ServiceFee;
use App\Service;
use App\ServiceShipping;
use App\WareHouse;

class ShippingChinaVietnam extends AbstractService {

    function __construct($data) {
        $this->service = new Service();

        $this->model = new ServiceShipping();
        foreach($data as $key => $val):
            $this->{$key} = $val;
        endforeach;

        #region Lay ra cong thuc tinh phi
        $result = $this->model->getCurrentPolicy($this->weight, $this->destination_warehouse, $this->apply_time);
        if($result):
            $this->weight_fee_first = $result->weight_fee_first;
            $this->unit_price = $result->weight_fee;
        endif;
        #endregion

//        #region Lay ra phi co dinh doi voi dich vu van chuyen TQ - VN
//        $this->fixed_fee = $this->service->getFixedFeeWithServiceCode($this->getServiceCode());
//        #endregion
    }

    function getServiceCode()
    {
        return $this->service_code;
    }

    /**
     * @desc Phi van chuyen quoc te TQ - VN duoc tinh bang
     * - phi co dinh (cau hinh trong bang services)
     * - phi van chuyen = can nang * don gia (trong bang service_shipping)
     * @return mixed
     */
    function calculatorFee()
    {
        if($this->weight > 0 && $this->weight < 0.5){
            $this->weight = 0.5;
        }

        if($this->weight <= 1){

            if($this->weight_fee_first > 0){
                return $this->weight_fee_first * $this->weight;
            }else{
                return $this->unit_price * $this->weight;
            }

        }else{

            if($this->weight_fee_first > 0){

                return $this->weight_fee_first + ($this->unit_price * ($this->weight - 1));

            }else{
                return $this->unit_price * $this->weight;
            }

        }

    }
}