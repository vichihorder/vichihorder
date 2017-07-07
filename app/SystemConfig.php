<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemConfig extends Model
{
    const CACHE_SYSTEM_CONFIG_KEY = 'system_config';

    protected $table = 'system_config';

    public static $system_config_data = [
        [
            'field_name' => 'Hệ thống tự động chuyển đơn ở trạng thái đang giao hàng sang đã nhận hàng',
            'key' => 'day_auto_change_order_receive'
        ],

        [
            'field_name' => 'ID nhóm mua hàng',
            'key' => 'group_crane_buying_id'
        ],

        [
            'field_name' => 'Phần trăm mua hàng phải mặc cả tối thiếu',
            'key' => 'crane_buying_percent_min'
        ],

        [
            'field_name' => 'Miễn phí mua hàng với ? đơn đầu tiên',
            'key' => 'order_buying_number_fee'
        ],
        [
            'field_name' => 'Thời gian hưởng hoa hồng của khách giới thiệu & sử dụng hệ thống (tính bằng tháng)',
            'key' => 'user_refer_time_enjoyment'
        ],
        [
            'field_name' => 'Tối đa địa chỉ nhận hàng khách',
            'key' => 'user_address_max'
        ],
        [
            'field_name' => 'Tối đa số điện thoại khách',
            'key' => 'user_mobile_max'
        ],
        [
            'field_name' => 'Tỉ lệ đặt cọc đơn hàng',
            'key' => 'order_deposit_percent'
        ],
        [
            'field_name' => 'Nếu kiện hàng >= ?kg thì tiến hành chuyển thẳng, không thì sẽ về kho phân phối tại Việt Nam',
            'key' => 'package_weight_transport_straight'
        ],

        [
            'field_name' => 'Hiển thị popup ngoài trang chủ?',
            'key' => 'home_page_enable_popup',
            'tag_name' => 'select',
            'options' => [
                0 => 'Không hiển thị',
                1 => 'Hiển thị',
            ]
        ],
        [
            'field_name' => 'Tiêu đề popup ngoài trang chủ',
            'key' => 'home_page_title_popup'
        ],
        [
            'field_name' => 'Nội dung popup ngoài trang chủ',
            'key' => 'home_page_content_popup',
            'tag_name' => 'textarea'
        ],
    ];

    public function updateData($data_insert){
        if(count($data_insert)):
            $this->newQuery()->delete();

            $this->newQuery()->insert($data_insert);
        endif;

        return true;
    }

    public static function getConfigValueByKey($key){
        if(empty($key)):
            return null;
        endif;

        $system_config_row = SystemConfig::where(['config_key' => $key])->first();
        if($system_config_row instanceof SystemConfig){
            return $system_config_row->config_value;
        }

        return null;
    }
}
