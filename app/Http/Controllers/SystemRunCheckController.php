<?php

namespace App\Http\Controllers;

use App\Order;
use App\Permission;
use App\SystemConfig;
use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SystemRunCheckController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){
        $can_view = Permission::isAllow(Permission::PERMISSION_SYSTEM_RUN_CHECK);
        if(!$can_view){
            return redirect('403');
        }

        $problem_list = [
            [
                'label' => sprintf('Đơn hàng quá %s ngày chưa mua hàng kể từ khi khách đặt coc đơn', $this->__combobox_day_long_time('order_not_buy', 1))
            ],
            [
                'label' => sprintf('Đơn quá %s ngày người bán chưa phát hàng kể từ khi mua hàng', $this->__combobox_day_long_time('order_not_seller_delivery', 2))
            ],
            [
                'label' => sprintf('Đơn quá %s ngày kho trung quốc chưa nhận hàng, kể từ khi người bán giao hàng', $this->__combobox_day_long_time('order_not_receive_from_seller', 1))
            ],
            [
                'label' => sprintf('Đơn quá %s ngày mà chưa vận chuyển về việt nam, kể từ khi kho trung quốc nhận', $this->__combobox_day_long_time('order_not_transport', 1))
            ],
            [
                'label' => sprintf('Đơn quá %s ngày mà chưa về kho phân phối tại việt nam, kể từ sau khi băt đầu vận chuyển từ trung quốc về vn', $this->__combobox_day_long_time('order_not_waiting_delivery', 2))
            ],
            [
                'label' => sprintf('Đơn quá %s ngày mà chưa giao hàng cho khách, kể từ sau khi hàng về kho phân phối tại Việt Nam', $this->__combobox_day_long_time('order_not_delivering', 2))
            ],
        ];

        return view('system_run_check', [
            'page_title' => 'Kiểm soát vận hành',
            'problem_list' => $problem_list
        ]);
    }

    private function __combobox_day_long_time($problem_type, $selected = null){
        $max = 100;
        $html[] = "<select data-problem-type='".$problem_type."' name='' class='_day_long_time'>";
        for($i = 1; $i <= $max; $i++){
            $selected_item = ($selected == $i) ? ' selected ' : '';
            $html[] = sprintf("<option %s value='%s'>%s</option>", $selected_item, $i, $i);
        }
        $html[] = "</select>";
        return implode('', $html);
    }

    public function problemTypeHtml(Request $request){
        $problem_type = $request->get('problem_type');
        $long_time = $request->get('long_time');

        //danh sach nv mua hang
        $crane_buying_list = UserRole::findByRoleId(
            [ SystemConfig::getConfigValueByKey('group_crane_buying_id') ]
        );

        $view = View::make('problems/' . $problem_type, [
            'problem_type' => $problem_type,
            'long_time' => $long_time,
            'crane_buying_list' => $crane_buying_list,
            'crane_buying_selected' => $request->get('paid_staff_id')
        ]);
        $html = $view->render();

        return response()->json([ 'success' => true, 'message' => 'ok', 'html' => $html ]);
    }
}
