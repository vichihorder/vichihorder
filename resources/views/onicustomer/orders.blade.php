@extends($layout)

@section('page_title')
    {{$page_title}}
@endsection

@section('key_active', 'order')

@section('content')
    <div class="wrapper wrapper-content orders-page">
        <div class="row">
            <div class="col-sm-3 col-md-3 col-lg-3">
                <div class="order_sidebar" data-spy="affix" data-offset-top="11" data-offset-bottom="64">
                    <div class="ibox float-e-margins">
                        <div class="ibox-content orderbox-content">
                            <!--<a class="btn btn-block btn-primary compose-mail" href="#">Tạo đơn hàng mới</a>
                            <div class="space-25"></div>-->
                            <div class="file-manager">
                                <h3 style="text-transform: uppercase;">Đơn hàng</h3>
                                <ul class="folder-list m-b-md" style="padding: 0">
                                    <li>
                                        <a href="{{ url('don-hang') }}">
                                            Tất cả <span class="label label-warning pull-right">{{ $orders_count }}</span>
                                        </a>
                                    </li>

                                    @foreach($status_list as $status_list_item)
                                        <?php if($status_list_item['selected']) $selected='selected';
                                        else $selected='';?>
                                        <li>
                                            <a href="{{ url('don-hang?status='.$status_list_item['key']) }}" class="{{$selected}}" data-status="{{ $status_list_item['key'] }}">
                                                {{ $status_list_item['val']  }}
                                                @if($status_list_item['count'])
                                                    <span class="label label-warning pull-right">{{ $status_list_item['count']  }}</span>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-9 col-md-9 col-lg-9 order-box animated fadeInRight m-b-md">
                <div class="order-box-header">
                    <div class="order-tools tooltip-demo">
                        <div class="row">
                            <!-- Ma don hang -->
                            <form onchange="this.submit();" action="{{ url('don-hang')  }}" method="get" id="_form-code-orders">
                                <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="order_code">Mã đơn hàng</label>
                                    <input type="text" class="form-control input-sm" placeholder="Nhập mã đơn hàng" id="order_code" name="order_code" value="{{  @$params['order_code'] }}">
                                </div>
                            </div>
                            </form>
                            <!-- Thoi gian -->
                            <form onchange="this.submit();" action="{{ url('don-hang')  }}" method="get" id="_form-datetime-orders">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Thời gian</label>

                                        <input type="hidden" name="status_time" id="status_time" value="{{ @$params['status_time'] ? @$params['status_time'] : 'ALL' }}">
                                        <div class="input-daterange input-group" id="datepicker">
                                            <div class="input-group-btn dropdown">
                                                <button id="status_time_btn" type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                                    @if(isset($params['status_time']) && $params['status_time'] != 'ALL')
                                                        @foreach($status_list as $status_list_item)
                                                            @if($params['status_time'] == $status_list_item['key'])
                                                                {{ $status_list_item['val'] }}
                                                                @break
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        Trạng thái
                                                    @endif
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="javascript:void(0)" data-status="ALL">Trạng thái</a></li>

                                                    @foreach($status_list as $status_list_item)

                                                        @if(isset($params['status_time']) && $params['status_time'] == $status_list_item['key'])
                                                            <li class="active">
                                                        @else
                                                            <li>
                                                        @endif

                                                            <a href="javascript:void(0)" data-status="{{ $status_list_item['key'] }}">{{ $status_list_item['val']  }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <input type="text" class="input-sm form-control" name="start" value="{{  @$params['start'] }}"/>
                                            <span class="input-group-addon">đến</span>
                                            <input type="text" class="input-sm form-control" name="end" value="{{  @$params['end'] }}" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- Trang thai -->
                            <form onchange="this.submit();" action="{{ url('don-hang')  }}" method="get" id="_form-orders">
                                <div class="col-sm-12">
                                <div>
                                    @foreach($status_list as $status_list_item)
                                        @if($status_list_item['selected'])
                                            <a class="btn btn-sm btn-danger m-b-xs _select-order-status selected" href="javascript:void(0)" data-status="{{ $status_list_item['key'] }}">
                                                <i class="fa fa-times" aria-hidden="true"></i> {{ $status_list_item['val']  }}
                                            </a>
                                        @else
                                            <a class="btn btn-sm btn-primary m-b-xs _select-order-status" href="javascript:void(0)" data-status="{{ $status_list_item['key'] }}">
                                                <span>{{ $status_list_item['val']  }}</span>
                                            </a>
                                        @endif
                                    @endforeach
                                    <input type="hidden" name="status" value="">
                                </div>
                            </div>
                            </form>
                        </div>

                    </div>
                </div>
                <div class="alert alert-warning">
                    Đơn hàng ở trạng thái "Đang Giao Hàng", sau 3 ngày khách không ấn "Đã Nhận", hệ thống sẽ tự động chuyển sang trạng thái "Đã Nhận".
                </div>
                <div class="order-box-content">
                    @if(count($orders))
                        <table class="footable table table-stripped toggle-arrow-tiny table-hover table-order">
                            <thead>
                            <tr>
                                <th>Thông tin đơn hàng</th>
                                <th data-sort-ignore="true" data-hide="phone,tablet">Phí trên đơn</th>
                                <th width="200" data-sort-ignore="true" data-hide="phone">Thời gian</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $order)
                                <tr class="order-item">
                                    <td data-value="{{$order->code}}">
                                        <div class="item-inner">
                                            <div class="item-thumb">
                                                <a href="{{ url('don-hang', $order->id)  }}" title="Xem chi tiết đơn hàng">
                                                    <img src="{{ $order->avatar }}" class="img-responsive">
                                                    {!! App\Util::showSite($order->site) !!}
                                                </a>
                                            </div>
                                            <div class="item-details">
                                                <a href="{{ url('don-hang', $order->id)  }}" title="Xem chi tiết đơn hàng">
                                                    <h3 class="item-title">#{{$order->code}}
                                                        <span id="_order_status_{{  $order->id }}"class="label label-primary" style="top: -2px; position: relative; margin-left: 10px;">
                                                            {{ App\Order::getStatusTitle($order->status)  }}
                                                        </span>
                                                    </h3>
                                                </a>
                                                <p class="m-b-none">Tiền hàng: {!! App\Util::formatNumber($order->amount * $order->exchange_rate) !!} <sup>đ</sup></p>
                                                <p class="m-b-none">Đặt cọc: {!! App\Util::formatNumber($order->deposit_amount) !!} <sup>đ</sup> ({!! App\Util::formatNumber($order->deposit_percent) !!}%)</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small>
                                            @foreach($order->order_fee as $order_fee_item)
                                                <p>
                                                    {!! $order_fee_item['label'] !!}:
                                                    <span class="text-danger">
                                                        <strong>{{$order_fee_item['value']}}<sup>đ</sup></strong>
                                                    </span>
                                                </p>
                                            @endforeach
                                        </small>
                                    </td>
                                    <td>
                                        <small id="_time_change_status_{{$order->id}}" class="more" data-max-height="100">
                                            <?php
                                            foreach(array_reverse(App\Order::$timeListOrderDetail) as $k => $v){
                                            if(empty($order->$k)){
                                                continue;
                                            }
                                            ?>
                                            <p>{{$v}}: {{ App\Util::formatDate($order->$k) }}</p>
                                            <?php } ?>
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="no-content">
                            <i class="fa fa-shopping-basket icon"></i>
                            <h4 class="title">Hiện chưa có đơn hàng!</h4>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('header-scripts')
    <link href="{!! asset('oniasset/css/plugins/footable/footable.core.css') !!}" rel="stylesheet"/>
    <link href="{!! asset('oniasset/css/plugins/datapicker/datepicker3.css') !!}" rel="stylesheet"/>
    <link href="{!! asset('oniasset/css/plugins/select2/select2.min.css') !!}" rel="stylesheet"/>
@endsection

@section('footer-scripts')
    <script src="{{ asset('oniasset/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('oniasset/js/plugins/footable/footable.all.min.js') }}"></script>
    <script src="{{ asset('oniasset/js/plugins/select2/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.footable').footable();
            $('.input-daterange').datepicker({
                format: 'dd-mm-yyyy',
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true
            });
            $(".select2_demo_2").select2();


            $("#datepicker .dropdown-menu li a").click(function(){
                $(this).parents(".dropdown").find('.btn').html($(this).text() + ' <span class="caret"></span>');
                $(this).parents(".dropdown").find('.btn').val($(this).data('value'));
                $('#status_time').val($(this).data('status'));
                $('#_form-datetime-orders').submit();
            });
        });
    </script>

    @parent
    <script type="text/javascript" src="{{ asset('js/jquery.lazy.min.js') }}"></script>
    <script>
        $(function() {

            $(document).on('click', '._select-order-status', function(){
                var selected = $(this).hasClass('selected');
                if(selected){
                    $(this).removeClass('selected');
                }else{
                    $(this).addClass('selected');
                }

                var order_status_list = [];
                $('._select-order-status.selected').each(function(){
                    order_status_list.push($(this).data('status'));
                });

                $('[name="status"]').val(order_status_list.join('-'));

                $('#_form-orders').submit();
            });

            /**
             * event xay ra khi click nut da nhan hang
             */
            $(document).on('click','._btn_change_status',function () {
                var order_id = $(this).data('order-id');
                $("#_btn_change_status_"+order_id).prop('disabled',true);
                $.ajax({
                    url : '/change-status-order',
                    type : 'POST',
                    data : {
                        order_id : order_id
                    }
                }).done(function (response) {
                    if(response.type == 'success'){
                        $("#_order_status_"+order_id).html('Đã nhận hàng');
                        $("#_btn_change_status_"+order_id).addClass('hidden');
                        $("#_time_change_status_"+order_id).append("<p>Đã nhận hàng:</p>"+response.date);

                    }else{
                        $("#_btn_change_status_"+order_id).prop('disabled',false);
                    }
                });
            });
        });
    </script>
@endsection

