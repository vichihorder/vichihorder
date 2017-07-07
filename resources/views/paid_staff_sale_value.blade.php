@extends('layouts.app')

@section('page_title')
    {{$page_title}}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                @include('partials/__breadcrumb',
                    [
                        'urls' => [
                            ['name' => 'Trang chủ', 'link' => url('home')],
                            ['name' => $page_title, 'link' => null],
                        ]
                    ]
                )
                <div class="card-body">

                    <div class="row">
                        <form action="{{ url('PaidStaffSaleValue')  }}"
                              method="get"
                              onchange="this.submit();">

                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <select class="_select_month form-control" name="month" id="">

                                                <?php
                                                $max = 10;
                                                for($i = 0; $i < $max; $i++){

                                                    $date = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
                                                    $date->modify("-{$i} month");

                                                    $month_choose = date('m');
                                                    if(request()->get('month')){
                                                        $month_choose = explode('_', request()->get('month'))[0];
                                                    }

                                                    $selected = $date->format('m') == $month_choose
                                                        ? ' selected ' : '';


                                                    echo sprintf("<option %s value='%s'>Tháng %s</option>", $selected, $date->format('m') . '_' . $date->format('Y'), $date->format('m/Y'));
                                                }
                                                ?>
                                            </select>
                                        </div>

                                    </div>


                                </div>


                        </form>
                    </div>

                    <div class="row">
                        <h5>*** Lưu ý: </h5>
                        <i class="order-not-original-amount box-color"></i> Đơn chưa nhập giá thực mua <br>
                    </div>

                    @if(count($crane_buying_list))
                        @foreach($crane_buying_list as $crane_buying_list_item)

                            <div class="row" style="margin-bottom: 30px;">
                                <h1>
                                    <a href="{{ url('user/detail', $crane_buying_list_item->id)  }}">{{$crane_buying_list_item->name}}</a>


                                    <?php
                                    $can_setup_paid_staff_value = App\Permission::isAllow(App\Permission::PERMISSION_SETUP_PAID_STAFF_SALE_VALUE);
                                    ?>

                                    @if($can_setup_paid_staff_value)
                                    <a

                                            class="" data-toggle="modal" href='#modal-crane-buying-setup-{{$crane_buying_list_item->id}}'>

                                        <i
                                                data-toggle="tooltip"
                                                title="Cấu hình lương"
                                                class="fa fa-cog" aria-hidden="true"></i>

                                    </a>
                                    @endif

                                    <div class="modal fade" id="modal-crane-buying-setup-{{$crane_buying_list_item->id}}">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title">Cấu hình bảng lương - {{$crane_buying_list_item->name}}</h4>
                                                </div>
                                                <div class="modal-body">

                                                    <div id="content-crane-buying-setup">

                                                        <table class="table table-striped table-bordered setting-list">
                                                            <thead>
                                                            <tr>
                                                                <th>Từ</th>
                                                                <th>Đến</th>
                                                                <th>Lương cơ bản</th>
                                                                <th>HH tối đa %</th>
                                                                <th>HH tối thiểu %</th>
                                                                <th>Mặc cả tối thiểu %</th>
                                                                <th></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>

                                                                @if(isset($crane_value_setting_list[$crane_buying_list_item->id])
                                                                && count($crane_value_setting_list[$crane_buying_list_item->id]))

                                                                    @foreach($crane_value_setting_list[$crane_buying_list_item->id] as $crane_value_setting_list_item)
                                                                        <tr class="_row">
                                                                            <td>
                                                                                <select name="start_month">
                                                                                    <?php
                                                                                    for($i = 1; $i <= 12; $i++){
                                                                                        $selected = $i == date('m', strtotime($crane_value_setting_list_item->activated_at)) ? ' selected ' : '';
                                                                                        echo sprintf("<option %s value='%s'>%s</option>", $selected, $i, $i);
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                                <select name="start_year">
                                                                                    <?php
                                                                                    for($ii = $begin_year; $ii <= $end_year; $ii++){
                                                                                        $selected = $i == date('Y', strtotime($crane_value_setting_list_item->activated_at)) ? ' selected ' : '';
                                                                                        echo sprintf("<option %s value='%s'>%s</option>", $selected, $ii, $ii);
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                            <td>

                                                                                <select name="end_month">
                                                                                    <?php
                                                                                    for($j = 1; $j <= 12; $j++){
                                                                                        $selected = $j == date('m', strtotime($crane_value_setting_list_item->deadlined_at)) ? ' selected ' : '';
                                                                                        echo sprintf("<option %s value='%s'>%s</option>", $selected, $j, $j);
                                                                                    }
                                                                                    ?>
                                                                                </select>

                                                                                <select name="end_year">
                                                                                    <?php
                                                                                    for($jj = $begin_year; $jj <= $end_year; $jj++){
                                                                                        $selected = $jj == date('Y', strtotime($crane_value_setting_list_item->deadlined_at)) ? ' selected ' : '';
                                                                                        echo sprintf("<option %s value='%s'>%s</option>", $selected, $jj, $jj);
                                                                                    }
                                                                                    ?>
                                                                                </select>

                                                                            </td>
                                                                            <td>
                                                                                <input type="number" name="salary_basic" value="{{$crane_value_setting_list_item->salary_basic}}">
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" name="rose_percent" value="{{$crane_value_setting_list_item->rose_percent}}">
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" name="rose_percent_min" value="{{$crane_value_setting_list_item->rose_percent_min}}">
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" name="require_min_bargain_percent" value="{{$crane_value_setting_list_item->require_min_bargain_percent}}">
                                                                            </td>
                                                                            <td>
                                                                                <a href="javascript:void(0)" class="_remove-row"><i class="fa fa-times"></i></a>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <a href="javascript:void(0)"
                                                    class="_add-config"
                                                    ><i class="fa fa-plus"></i> Thêm cấu hình</a>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                                                    <button type="button"

                                                            data-paid-user-id="{{$crane_buying_list_item->id}}"
                                                            class="btn btn-primary _save-setting">Lưu</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <small>{{$crane_buying_list_item->code}}</small>
                                </h1>

                                <h3
                                        style="cursor: pointer"
                                        data-toggle="collapse" data-target="#order-overrun-{{$crane_buying_list_item->id}}">Đơn hàng doanh số <i data-toggle="tooltip" title="" class="fa fa-question-circle" aria-hidden="true" data-original-title="Là những đơn khách đã nhận hàng trong tháng này"></i><small>(
                                        @if(isset($orders_overrun_list[$crane_buying_list_item->id]))
                                            {{count($orders_overrun_list[$crane_buying_list_item->id])}}
                                        @else
                                            0
                                        @endif
                                        )</small></h3>

                                <div id="order-overrun-{{$crane_buying_list_item->id}}" class="collapse">
                                    <?php
                                    $total_amount_customer = 0;
                                    $total_amount_original = 0;
                                    $total_amount_bargain = 0;

                                    $total_amount_customer_vnd = 0;
                                    $total_amount_original_vnd = 0;
                                    $total_amount_bargain_vnd = 0;

                                    $total_amount_bargain_real_cal = 0;
                                    $total_amount_bargain_real_cal_vnd = 0;
                                    ?>

                                    @if(isset($orders_overrun_list[$crane_buying_list_item->id]))

                                        <table class="table table-hover table-striped">
                                            <thead>
                                            <tr>
                                                <td>TT</td>
                                                <td>Đơn hàng</td>
                                                <td class="text-right">(1) Báo khách <i class="fa fa-question-circle" data-toggle="tooltip" title="tiền hàng + ship nội dịa TQ"></i></td>
                                                <td class="text-right">(2) Thực mua <i class="fa fa-question-circle" data-toggle="tooltip" title="tiền hàng + ship nội dịa TQ"></i></td>
                                                <td class="text-right">(1) - (2) Mặc cả</td>
                                                <td class="text-right">Nhận</td>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($orders_overrun_list[$crane_buying_list_item->id] as $idx => $orders_overrun_list_item)
                                                <tr
                                                        class="
                                                        @if($orders_overrun_list_item->amount_original <= 0)
                                                            order-not-original-amount
                                                        @endif
                                                    "
                                                >
                                                    <td>{{$idx+1}}</td>
                                                    <td>
                                                        <a href="{{url('order/detail', $orders_overrun_list_item->id)}}">{{$orders_overrun_list_item->code}}</a>
                                                        <small>({{App\Order::getStatusTitle($orders_overrun_list_item->status)}})</small>

                                                        @if($orders_overrun_list_item->bought_at)

                                                            <p>Mua: {{App\Util::formatDate($orders_overrun_list_item->bought_at)}}</p>
                                                        @endif

                                                        @if($orders_overrun_list_item->received_at)
                                                            <p>Nhận: {{App\Util::formatDate($orders_overrun_list_item->received_at)}}</p>
                                                        @endif
                                                    </td>
                                                    <td class="text-right text-danger">
                                                        {{App\Util::formatNumber($orders_overrun_list_item->customer_amount)}}¥
                                                        /
                                                        {{App\Util::formatNumber($orders_overrun_list_item->customer_amount_vnd)}}đ
                                                    </td>
                                                    <td class="text-right text-danger">
                                                        {{App\Util::formatNumber($orders_overrun_list_item->amount_original)}}¥
                                                        /
                                                        {{App\Util::formatNumber($orders_overrun_list_item->amount_original_vnd)}}đ
                                                    </td>
                                                    <td class="text-right text-danger">
                                                        {{App\Util::formatNumber($orders_overrun_list_item->amount_bargain)}}¥ /
                                                        {{App\Util::formatNumber($orders_overrun_list_item->amount_bargain_vnd)}}đ
                                                    </td>
                                                    <td class="text-right text-danger">

                                                        {{App\Util::formatNumber($orders_overrun_list_item->amount_bargain_real_cal)}}¥ /
                                                        {{App\Util::formatNumber($orders_overrun_list_item->amount_bargain_real_cal_vnd)}}đ
                                                        <br>

                                                        <i style="color: #000;"
                                                           class="fa fa-question-circle"
                                                           data-toggle="tooltip"
                                                           title=""
                                                           data-original-title="{{App\Util::formatNumber($orders_overrun_list_item->percent_real_cal)}}%"></i>


                                                    </td>
                                                </tr>

                                                <?php
                                                $total_amount_customer += $orders_overrun_list_item->customer_amount;
                                                $total_amount_original += $orders_overrun_list_item->amount_original;
                                                $total_amount_bargain += $orders_overrun_list_item->amount_bargain;

                                                $total_amount_customer_vnd += $orders_overrun_list_item->customer_amount_vnd;
                                                $total_amount_original_vnd += $orders_overrun_list_item->amount_original_vnd;
                                                $total_amount_bargain_vnd += $orders_overrun_list_item->amount_bargain_vnd;

                                                $total_amount_bargain_real_cal += $orders_overrun_list_item->amount_bargain_real_cal;
                                                $total_amount_bargain_real_cal_vnd += $orders_overrun_list_item->amount_bargain_real_cal_vnd;
                                                ?>
                                            @endforeach

                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td class="text-right">
                                                    <h5>

                                                        {{App\Util::formatNumber($total_amount_customer)}}¥ / {{App\Util::formatNumber($total_amount_customer_vnd)}} đ

                                                    </h5>
                                                </td>
                                                <td class="text-right">
                                                    <h5>

                                                        {{App\Util::formatNumber($total_amount_original)}}¥ / {{App\Util::formatNumber($total_amount_original_vnd)}} đ

                                                    </h5>
                                                </td>
                                                <td class="text-right">
                                                    <h5>
                                                        {{App\Util::formatNumber($total_amount_bargain)}}¥ / {{App\Util::formatNumber($total_amount_bargain_vnd)}} đ
                                                    </h5>
                                                </td>

                                                <td class="text-right">
                                                    <h5>
                                                        {{App\Util::formatNumber($total_amount_bargain_real_cal)}}¥ / {{App\Util::formatNumber($total_amount_bargain_real_cal_vnd)}} đ
                                                    </h5>
                                                </td>
                                            </tr>

                                            </tbody>
                                        </table>
                                    @else
                                        <h6>Không có gì!</h6>
                                    @endif
                                </div>



                                <h3
                                        style="cursor: pointer"
                                        data-toggle="collapse" data-target="#order-buying-{{$crane_buying_list_item->id}}">Đơn hàng phát sinh <i data-toggle="tooltip" title="" class="fa fa-question-circle" aria-hidden="true" data-original-title="Là những đơn khách chưa nhận hàng trong tháng này"></i> <small>(
                                        @if(isset($orders_buying_list[$crane_buying_list_item->id]))
                                            {{count($orders_buying_list[$crane_buying_list_item->id])}}
                                        @else
                                            0
                                        @endif
                                        )</small></h3>

                                <div id="order-buying-{{$crane_buying_list_item->id}}" class="collapse">
                                    <?php
                                    $total_amount_customer1 = 0;
                                    $total_amount_original1 = 0;
                                    $total_amount_bargain1 = 0;

                                    $total_amount_customer_vnd1 = 0;
                                    $total_amount_original_vnd1 = 0;
                                    $total_amount_bargain_vnd1 = 0;
                                    ?>

                                    @if(isset($orders_buying_list[$crane_buying_list_item->id]))
                                        <table class="table table-hover table-striped">

                                            <thead>
                                            <tr>
                                                <td>TT</td>
                                                <td>Đơn hàng</td>
                                                <td class="text-right">(1) Báo khách <i class="fa fa-question-circle" data-toggle="tooltip" title="tiền hàng + ship nội dịa TQ"></i></td>
                                                <td class="text-right">(2) Thực mua <i class="fa fa-question-circle" data-toggle="tooltip" title="tiền hàng + ship nội dịa TQ"></i></td>
                                                <td class="text-right">(1) - (2) Mặc cả</td>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($orders_buying_list[$crane_buying_list_item->id] as $idx => $orders_buying_list_item)
                                                <tr
                                                        class="
                                                        @if($orders_buying_list_item->amount_original <= 0)
                                                                order-not-original-amount
                                                            @endif
                                                                "
                                                >
                                                    <td>{{$idx+1}}</td>
                                                    <td>
                                                        <a href="{{url('order/detail', $orders_buying_list_item->id)}}">{{$orders_buying_list_item->code}}</a>
                                                        <small>({{App\Order::getStatusTitle($orders_buying_list_item->status)}})</small>

                                                        @if($orders_buying_list_item->bought_at)

                                                            <p>Mua: {{App\Util::formatDate($orders_buying_list_item->bought_at)}}</p>
                                                        @endif

                                                        @if($orders_buying_list_item->received_at)
                                                            <p>Nhận: {{App\Util::formatDate($orders_buying_list_item->received_at)}}</p>
                                                        @endif

                                                    </td>
                                                    <td class="text-right text-danger">
                                                        {{App\Util::formatNumber($orders_buying_list_item->customer_amount)}}¥
                                                        /
                                                        {{App\Util::formatNumber($orders_buying_list_item->customer_amount_vnd)}}đ
                                                    </td>
                                                    <td class="text-right text-danger">
                                                        {{App\Util::formatNumber($orders_buying_list_item->amount_original)}}¥
                                                        /
                                                        {{App\Util::formatNumber($orders_buying_list_item->amount_original_vnd)}}đ
                                                    </td>
                                                    <td class="text-right text-danger">
                                                        {{App\Util::formatNumber($orders_buying_list_item->amount_bargain)}}¥ /
                                                        {{App\Util::formatNumber($orders_buying_list_item->amount_bargain_vnd)}}đ
                                                    </td>

                                                </tr>

                                                <?php
                                                $total_amount_customer1 += $orders_buying_list_item->customer_amount;
                                                $total_amount_original1 += $orders_buying_list_item->amount_original;
                                                $total_amount_bargain1 += $orders_buying_list_item->amount_bargain;

                                                $total_amount_customer_vnd1 += $orders_buying_list_item->customer_amount_vnd;
                                                $total_amount_original_vnd1 += $orders_buying_list_item->amount_original_vnd;
                                                $total_amount_bargain_vnd1 += $orders_buying_list_item->amount_bargain_vnd;
                                                ?>
                                            @endforeach

                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td class="text-right">
                                                    <h5>

                                                        {{App\Util::formatNumber($total_amount_customer1)}}¥ / {{App\Util::formatNumber($total_amount_customer_vnd1)}} đ

                                                    </h5>
                                                </td>
                                                <td class="text-right">
                                                    <h5>

                                                        {{App\Util::formatNumber($total_amount_original1)}}¥ / {{App\Util::formatNumber($total_amount_original_vnd1)}} đ

                                                    </h5>
                                                </td>
                                                <td class="text-right">
                                                    <h5>
                                                        {{App\Util::formatNumber($total_amount_bargain1)}}¥ / {{App\Util::formatNumber($total_amount_bargain_vnd1)}} đ
                                                    </h5>
                                                </td>
                                            </tr>

                                            </tbody>
                                        </table>
                                    @else
                                        <h6>Không có gì!</h6>
                                    @endif
                                </div>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">Thống kê mua hàng trong tháng</legend>
                                    <div class="control-group">
                                        <h3
                                                class="

                                        @if($crane_buying_list_item->is_bargain_target == true)
                                                        target_success
                                                        @endif


                                                @if($crane_buying_list_item->is_bargain_target == false)
                                                        target_fail
                                                        @endif

                                                        "
                                        >Báo khách / Thực mua / Mặc cả
                                            <i class="fa fa-question-circle"

                                               data-toggle="tooltip"
                                               data-html="true"
                                               title="Tháng này bạn cần mặc cả tối thiếu là @if($crane_buying_list_item->setting) {{$crane_buying_list_item->setting->require_min_bargain_percent}} @else 0 @endif%"
                                            ></i>

                                            :
                                            {{App\Util::formatNumber($crane_buying_list_item->amount_customer_current_month_vnd)}} đ /
                                            {{App\Util::formatNumber($crane_buying_list_item->amount_original_current_month_vnd)}} đ /
                                            {{App\Util::formatNumber($crane_buying_list_item->amount_bargain_current_month_vnd)}} đ ({{App\Util::formatNumber($crane_buying_list_item->percent_bargain_current_month)}}%)
                                        </h3>
                                    </div>
                                </fieldset>

                                <?php
                                $crane_value_finish = (float)$total_amount_bargain_real_cal_vnd;
                                if($crane_buying_list_item->setting){
                                    $crane_value_finish += (float)$crane_buying_list_item->setting->salary_basic;
                                }
                                ?>

                                <h3>Lương <i class="fa fa-question-circle"
                                    data-toggle="tooltip"
                                             data-html="true"
                                             title="
                                             @if($crane_buying_list_item->setting)
                                             <p>Lương cơ bản: {{ App\Util::formatNumber(($crane_buying_list_item->setting->salary_basic))  }} đ</p>
{{--                                             <p>Phần trăm tính doanh số: {{ App\Util::formatNumber($crane_buying_list_item->rose_percent_real_cal)  }}%</p>--}}
                                             @endif

                                             <p>Doanh số: {{App\Util::formatNumber($total_amount_bargain_real_cal_vnd)}} đ</p>
                                             "
                                    ></i>: {{ App\Util::formatNumber($crane_value_finish) }} đ</h3>

                            </div>

                        @endforeach

                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css_bottom')
    @parent

    <style>
        .done{
            background: rgba(41, 199, 95, 0.65);
        }
        .not-done{
            background: rgba(251, 255, 150, 0.56);
        }
        .box-color{
            width: 20px;
            height: 20px;
            display: inline-block;
            float: left;
            margin-right: 10px;
        }
        .order-not-original-amount{
            background: rgba(208, 89, 89, 0.23)!important;
        }
        .setting-list td, th{
            padding: 5px!important;
        }
        .target_success{
            color: green;
        }

        .target_fail{
            color: red;
        }

        fieldset.scheduler-border {
            border: 1px groove #ddd !important;
            padding: 0 1.4em 1.4em 1.4em !important;
            margin: 0 0 1.5em 0 !important;
            -webkit-box-shadow:  0px 0px 0px 0px #000;
            box-shadow:  0px 0px 0px 0px #000;

        }

        legend.scheduler-border {
            font-size: 1.2em !important;
            font-weight: bold !important;
            text-align: left !important;
            border:none;
            width:300px;

        }
    </style>

@endsection

@section('js_bottom')
    @parent
    <script src="{{ asset('js/bootstrap-datepicker.js')  }}"></script>
    <script>
        $(document).ready(function(){

            $('._datepicker').datepicker({

            });

            $(document).on('click', '._add-config', function(){

                var parent = $(this).parents('.modal-body');
                var row = '<tr class="_row">';
                    row += '<td>';
                        row += '<select name="start_month">';
                        for(var i = 1; i <= 12; i++){
                            row += '<option value="' +i+ '">'+i+'</option>';
                        }
                        row += '</select>';

                        row += '<select name="start_year">';
                        for(var j = parseInt("{{$begin_year}}"); j <= parseInt("{{$end_year}}"); j++){
                            row += '<option value="' +j+ '">'+j+'</option>';
                        }
                        row += '</select>';

                    row += '</td>';

                    row += '<td>';

                        row += '<select name="end_month">';
                        for(var i = 1; i <= 12; i++){
                            row += '<option value="' +i+ '">'+i+'</option>';
                        }
                        row += '</select>';

                        row += '<select name="end_year">';
                        for(var j = parseInt("{{$begin_year}}"); j <= parseInt("{{$end_year}}"); j++){
                            row += '<option value="' +j+ '">'+j+'</option>';
                        }
                        row += '</select>';

                    row += '</td>';
                    row += '<td><input type="number" name="salary_basic" value="0" /></td>';
                    row += '<td><input type="number" name="rose_percent" value="0" /></td>';
                    row += '<td><input type="number" name="rose_percent_min" value="0" /></td>';
                    row += '<td><input type="number" name="require_min_bargain_percent" value="0" /></td>';
                    row += '<td><a href="javascript:void(0)" class="_remove-row"><i class="fa fa-times"></i></a></td>';
                    row += '</tr>';

                parent.find('tbody').append(row);
                parent.find('tbody tr:last select:first').focus();

            });

            $(document).on('click', '._remove-row', function(){
                $(this).parents('._row').remove();
            });

            $(document).on('click', '._save-setting', function(){
                var paid_user_id = $(this).data('paid-user-id');
                var items = [];
                var parent = $(this).parents('.modal');
                parent.find('._row').each(function(i){
                    items.push({
                        start_month:$(this).find('select[name="start_month"]').val(),
                        start_year:$(this).find('select[name="start_year"]').val(),
                        end_month:$(this).find('select[name="end_month"]').val(),
                        end_year:$(this).find('select[name="end_year"]').val(),
                        salary_basic:$(this).find('input[name="salary_basic"]').val(),
                        rose_percent:$(this).find('input[name="rose_percent"]').val(),
                        require_min_bargain_percent:$(this).find('input[name="require_min_bargain_percent"]').val(),
                        rose_percent_min:$(this).find('input[name="rose_percent_min"]').val()
                    });
                });
                request("{{url('PaidStaffSaleValue/Setting')}}", "post", {
                    paid_user_id:paid_user_id,
                    items:items
                }).done(function(response){
                    if(response.success){

                        $.notify("Lưu thành công", {type:"success"});
                        $('#modal-crane-buying-setup-' + paid_user_id).modal('hide');

                    }else{
                        $.notify("Lưu không thành công", {type:"error"});
                    }
                });
            });
        });

    </script>
@endsection

