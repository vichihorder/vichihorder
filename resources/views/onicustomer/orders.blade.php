@extends($layout)

@section('page_title')
    {{$page_title}}
@endsection

@section('key_active', 'order')

@section('content')
    <div class="row">
        <div class="col-sm-3 col-md-3 col-lg-3">
                <div class="order_sidebar">
                    <div class="list-group menu-message" style="margin-top: 0;">
                        <a href="{{ url('don-hang') }}" class="list-group-item {{ empty($params['status']) ? 'active' : ''}}">
                            <!--<i class="icon-inbox"></i>--> Tất cả <span class="badge pull-right">{{ $orders_count }}</span>
                        </a>
                        @foreach($status_list as $status_list_item)
                            <?php if($status_list_item['selected']) $selected='active';
                            else $selected='';?>
                        <a href="{{ url('don-hang?status='.$status_list_item['key']) }}" class="list-group-item {{$selected}}" data-status="{{ $status_list_item['key'] }}">
                            <!--<i class="icon-pencil"></i>-->{{ $status_list_item['val']  }}
                            @if($status_list_item['count'])
                                <span class="badge bg-green-1 pull-right">{{ $status_list_item['count']  }}</span>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
        </div>
        <div class="col-sm-9 col-md-9 col-lg-9 order-box animated fadeInRight m-b-md">
            <div class="mail-list">
                <div class="clearfix"></div>

                <!-- Toolbar message -->
                <div class="data-table-toolbar">
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
                    </div>
                </div>
                <!-- End toolbar message -->

                <div class="alert alert-warning" style="border-left: 0; border-right: 0; margin-bottom: -1px;">
                    Đơn hàng ở trạng thái "Đang Giao Hàng", sau 3 ngày khách không ấn "Đã Nhận", hệ thống sẽ tự động chuyển sang trạng thái "Đã Nhận".
                </div>

                <div class="table-responsive">
                    @if(count($orders))
                    <table class="table table-hover table-message" style="margin-bottom: 0;">
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
                                                </h3>
                                            </a>
                                            <span id="_order_status_{{  $order->id }}"class="label label-primary" style="top: -2px; position: relative; margin-left: 10px;">
                                                {{ App\Order::getStatusTitle($order->status)  }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="more" data-max-height="90">
                                        @foreach($order->order_fee as $order_fee_item)
                                            <p>
                                                {!! $order_fee_item['label'] !!}:
                                                <span class="text-danger">
                                                        <strong>{{$order_fee_item['value']}}<sup>đ</sup></strong>
                                                    </span>
                                            </p>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <div id="_time_change_status_{{$order->id}}" class="more" data-max-height="90">
                                        <?php
                                        foreach(array_reverse(App\Order::$timeListOrderDetail) as $k => $v){
                                        if(empty($order->$k)){
                                            continue;
                                        }
                                        ?>
                                        <p>{{$v}}: {{ App\Util::formatDate($order->$k) }}</p>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                        <div class="no-content">
                            <i class="fa fa-shopping-cart icon"></i>
                            <h4 class="title">Hiện chưa có đơn hàng!</h4>
                        </div>
                    @endif
                </div>

                <div class="data-table-toolbar-footer">
                    <div class="pull-right">
                        {{ $orders->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('header-scripts')
@endsection

@section('footer-scripts')
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

