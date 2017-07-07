@extends($layout)

@section('page_title')
    {{@$page_title}}
@endsection

@section('content')
    <div id="_content">
        <div class="wrapper wrapper-content">
            <div class="ibox"">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="pull-right">
                                @if($permission['can_change_order_cancel'])
                                    <form class="___form">
                                        <input type="hidden" name="action" value="cancel_order">
                                        <input type="hidden" name="method" value="post">
                                        <input type="hidden" name="url" value="{{ url('don-hang/' .$order_id. '/hanh-dong')  }}">
                                        <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                        <input type="hidden" name="response" value="customer/order_detail">

                                        <a href="javascript:void(0)" class="btn btn-warning __cancelOrder">
                                            <i class="fa fa-ban" aria-hidden="true"></i> HỦY ĐƠN
                                        </a>
                                    </form>
                                @endif
                            </div>

                            <div class="m-b-md">
                                <!--<a href="#" class="btn btn-white btn-xs pull-right">Sửa đơn hàng</a>-->
                                <h2>Đơn hàng <strong>#{{$order->code}}</strong></h2>
                            </div>
                            <dl class="dl-horizontal">
                                <dt>Trạng thái:</dt>
                                <dd><span id="order_status" class="label label-primary">{{ App\Order::getStatusTitle($order->status) }}</span></dd>
                            </dl>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5">
                            <dl class="dl-horizontal">
                                <dt>Tỉ lệ đặt cọc (%):</dt>
                                <dd class="m-b-xs">{{$order->deposit_percent}} (<span class="text-danger">{{ App\Util::formatNumber($order->deposit_amount)}} <sup>đ</sup></span>)</dd>
                                <dt>Tỉ giá:</dt>
                                <dd class="m-b-xs">{{ App\Util::formatNumber($order->exchange_rate) }} <sup>đ</sup></dd>
                                <dt>Phí VC nội địa TQ:</dt>
                                <dd class="m-b-xs">¥{{ $order->domestic_shipping_fee  }}</dd>
                                <dt>Kho phân phối:</dt>
                                <dd>{{$order->destination_warehouse}}</dd>
                            </dl>
                        </div>
                        <div class="col-lg-7" id="cluster_info">
                            <dl class="dl-horizontal m-b-sm">
                                <dt>Dịch vụ:</dt>
                                <dd>
                                    @if(!empty($services))
                                        @foreach($services as $service)
                                            <form class="___form">
                                                <input type="hidden" name="action" value="choose_service">
                                                <input type="hidden" name="method" value="post">
                                                <input type="hidden" name="service" value="{{$service['code']}}">
                                                <input type="hidden" name="url" value="{{ url('don-hang/' .$order_id. '/hanh-dong')  }}">
                                                <input type="hidden" name="response" value="onicustomer/order_detail">
                                                <input type="hidden" name="_token" value="{{ csrf_token()  }}">

                                                <?php

                                                //                                                                var_dump($service);

                                                ?>
                                                <div class="checkbox m-t-none m-b-xs">
                                                    <input
                                                            class="___btn-action"
                                                            @if($service['checked']) checked @endif
                                                            @if($service['is_default']) disabled @endif
                                                            @if(!$permission['can_change_order_service']) disabled @endif
                                                            type="checkbox" id="checkbox_{{$service['code']}}"
                                                            value="{{$service['code']}}">
                                                    <label for="checkbox_{{$service['code']}}">
                                                        <i class="fa {{ App\Service::getServiceIcon($service['code'])  }}"></i> {{$service['name']}}
                                                    </label>
                                                </div>

                                            </form>
                                        @endforeach
                                    @else
                                        <p class="form-control-static">Không có dịch vụ nào</p>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                    @if($order->user_address_id)
                    <div class="row">
                        <div class="col-lg-12">
                            <dl class="dl-horizontal">
                                <dt>Đ/C nhận hàng:</dt>
                                <dd>
                                    <div>
                                        <strong>
                                            <i class="fa fa-user-circle-o" aria-hidden="true"></i> {{$user_address->reciver_name}} / <i class="fa fa-phone" aria-hidden="true"></i> {{$user_address->reciver_phone}}
                                        </strong>
                                    </div>
                                    <div>
                                        <i class="fa fa-map-marker" aria-hidden="true"></i> {{$user_address->detail}}, {{$user_address->district_label}}, {{$user_address->province_label}}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                    @endif

                    <div class="row m-t-sm">
                        <div class="col-lg-12">
                            <div class="panel blank-panel">
                                <div class="panel-heading">
                                    <div class="panel-options">
                                        <ul class="nav nav-tabs">
                                            <li class=""><a data-toggle="tab" href="#order-fee"> Phí trên đơn</a></li>
                                            <li class=""><a data-toggle="tab" href="#order-history"> Lịch sử trạng thái</a></li>
                                            <li class="active"><a data-toggle="tab" href="#order-transaction"> Lịch sử giao dịch</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="panel-body">
                                    <div class="tab-content">
                                        <div id="order-fee" class="tab-pane">
                                            <fieldset class="form-horizontal order-fee">
                                                @foreach($order_fee as $order_fee_item)
                                                    <div class="form-group col-sm-6 m-b-none">
                                                        <label class="col-sm-7 control-label">{!! $order_fee_item['label'] !!}: </label>
                                                        <div class="col-sm-5">
                                                            <p class="form-control-static">{{$order_fee_item['value']}} <sup>đ</sup></p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </fieldset>
                                        </div>
                                        <div id="order-history" class="tab-pane">
                                            <table class="table no-padding-leftright">
                                                <thead>
                                                <tr>
                                                    <th>Trạng thái</th>
                                                    <th>Thời gian</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $count = 0; ?>

                                                <?php
                                                foreach(App\Order::$timeListOrderDetail as $k => $v){
                                                $count++;
                                                if(empty($order->$k)){
                                                    continue;
                                                }
                                                ?>
                                                @if($count == 1)
                                                    <tr>
                                                        <td width="30%" class="border-top-none">{{$v}}</td>
                                                        <td class="border-top-none">{{ App\Util::formatDate($order->$k) }}</td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td>{{$v}}</td>
                                                        <td>{{ App\Util::formatDate($order->$k)  }}</td>
                                                    </tr>
                                                @endif
                                                <?php } ?>

                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="order-transaction" class="tab-pane active">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>Mã GD</th>
                                                    <th>Trạng thái</th>
                                                    <th>Giá trị</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($transactions as $transaction)
                                                    <?php
                                                    $user = App\User::find($transaction->user_id);
                                                    $order2 = App\Order::find($transaction->object_id);

                                                    if(!$user) $user = new App\User();
                                                    if(!$order2) $order2 = new App\Order();
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <p>Loại: {{ App\UserTransaction::$transaction_type[$transaction->transaction_type]  }} ({{ App\Util::formatDate($transaction->created_at)  }})</p>
                                                            {{$transaction->transaction_code}}<br>
                                                            <small class="" style="color: grey">{{$transaction->transaction_note}}</small>
                                                        </td>
                                                        <td>
                                                            <span class="@if($transaction->state == App\UserTransaction::STATE_COMPLETED) label label-success @endif">
                                                                {{ App\UserTransaction::$transaction_state[$transaction->state]  }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="text-danger">
                                                                {{ App\Util::formatNumber($transaction->amount) }}<sup>đ</sup>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="order-complaint" class="tab-pane">
                                            <a href="{{url('tao-khieu-nai',$order->id)}}"  class="btn btn-success"> Tạo khiếu nại</a>
                                            <table class="table">
                                                <thead class="thead-default">
                                                <tr>
                                                    <th>Tên KN</th>
                                                    <th>Trạng thái KN</th>
                                                    <th>Tiếp nhận</th>
                                                    <th>Hoàn thành</th>
                                                    <th>Từ Chối</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i = 1 ?>
                                                @foreach($list_complaint as $complaint)
                                                    <tr>

                                                        <td>{{ $complaint->title }}</td>
                                                        <td>{{ $complaint->status }}</td>
                                                        @if($complaint->accept_time)
                                                            <td>{{ $complaint->accept_time }}</td>
                                                        @else
                                                            <td>---</td>
                                                        @endif
                                                        @if( $complaint->finish_time )
                                                            <td>{{ $complaint->finish_time }}</td>
                                                        @else
                                                            <td>---</td>
                                                        @endif
                                                        @if($complaint->reject_time)
                                                            <td>{{ $complaint->reject_time }}</td>
                                                        @else
                                                            <td>---</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="order-detail-page">
                <div class="col-sm-12">
                    <?php
                    $total_order_quantity = 0;
                    $total_price_ndt = 0;
                    $total_price_vnd = 0;
                    if(count($order_items)){
                        foreach($order_items as $order_item){
                            $total_order_quantity += $order_item->order_quantity;
                            $total_price_ndt += $order_item->getPriceCalculator() * $order_item->order_quantity;
                            $total_price_vnd += $order_item->getPriceCalculator() * $order_item->order_quantity * $order->exchange_rate;
                        }
                    }
                    ?>
                    <div class="ibox">
                        <div class="ibox-content">
                            @if(count($order_items))
                            <div class="table-responsive">
                                <table class="table table-hover table-striped shoping-cart-table">
                                    <thead>
                                    <th colspan="2">Sản phẩm</th>
                                    <th class="text-right">Đơn giá</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-right">Tiền hàng</th>
                                    </thead>
                                    <tbody>
                                    @foreach($order_items as $order_item)
                                        <tr id="order-item-{{$order_item->id}}" class="order-item">
                                            <td width="90">
                                                <div class="cart-product-imitation" style="padding-top: 0;">
                                                    <img src="{{$order_item->image}}" style="width: 100%; height: 100%;">
                                                </div>
                                            </td>
                                            <td class="desc">
                                                <h3>
                                                    <a href="{{$order_item->link}}" target="_blank" title="Đễn trang sản phẩm">
                                                        <i class="fa fa-external-link" aria-hidden="true"></i>
                                                    </a>
                                                    <a href="{{$order_item->link}}" target="_blank" class="text-navy">
                                                        {{$order_item->title}}
                                                    </a>
                                                </h3>
                                                <!--/ Mo ta san pham -->
                                                <dl class="small m-b-none">
                                                    <dt style="display: inline-block; margin-right: 10px;">Thuộc tính:</dt>
                                                    <dd style="display: inline-block;">{{$order_item->property}}</dd>
                                                </dl>

                                                <form class="___form m-t-sm" onsubmit="return false;">
                                                    <input type="hidden" name="action" value="order_item_comment">
                                                    <input type="hidden" name="method" value="post">
                                                    <input type="hidden" name="item_id" value="{{$order_item->id}}">
                                                    <input type="hidden" name="url" value="{{ url('don-hang/' . $order_id . '/hanh-dong')  }}">
                                                    <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                                    <input type="hidden" name="response" value="onicustomer/order_detail">

                                                    <input style="max-width: 300px;" name="order_item_comment_message"
                                                           class="form-control input-sm m-b-xs ___input-action"
                                                           type="text"
                                                           data-key-global="order-item-comment-{{$order_item->id}}"
                                                           placeholder="Chat về sản phẩm...">

                                                </form>
                                                <ul class="list-unstyled m-b-none">
                                                    @if(!empty($order_item_comments[$order_item->id]))
                                                        @foreach($order_item_comments[$order_item->id] as $order_item_comment)
                                                            <?php
                                                            if(in_array($order_item_comment->type_context, [App\Comment::TYPE_CONTEXT_ACTIVITY, App\Comment::TYPE_CONTEXT_LOG]))
                                                                $cmt_class = "cmt_staff";
                                                            else
                                                                $cmt_class = "";
                                                            ?>
                                                            <li class="m-b-xs {{$cmt_class}}">
                                                                @if($order_item_comment->type_context != App\Comment::TYPE_CONTEXT_LOG)
                                                                    <strong>{{$order_item_comment->user->name}}</strong>
                                                                @endif
                                                                {{$order_item_comment->message}}
                                                                <small>
                                                                    {{ App\Util::formatDate($order_item_comment->created_at)  }}
                                                                </small>
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            </td>
                                            <td width="100">
                                                <span class="price_vnd">{{ App\Util::formatNumber($order_item->getPriceCalculator() * $order->exchange_rate) }}</span><sup>đ</sup>
                                                <p class="small text-muted">¥<span class="price">{{ $order_item->getPriceCalculator() }}</span></p>
                                            </td>
                                            <td width="100">
                                                <p class="form-control-static text-center">{{$order_item->order_quantity}}</p>
                                            </td>
                                            <td width="100">
                                                <strong>
                                                    <span class="sub_total_vnd">{{ App\Util::formatNumber($order_item->getPriceCalculator() * $order_item->order_quantity * $order->exchange_rate) }}</span><sup>đ</sup>
                                                </strong>
                                                <p class="small text-muted">¥<span class="sub_total">{{$order_item->getPriceCalculator() * $order_item->order_quantity}}</span></p>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                        <th colspan="3">Tổng cộng</th>
                                        <th class="text-center">{{ $total_order_quantity }}</th>
                                        <th class="text-right">
                                            <strong>
                                                <span class="sub_total_vnd">{{ App\Util::formatNumber($total_price_vnd)  }}</span><sup>đ</sup>
                                            </strong>
                                            <p class="small text-muted">¥<span class="sub_total">{{ $total_price_ndt  }}</span></p>
                                        </th>
                                    </tfoot>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if(count($packages) > 0)
                    <div class="ibox">
                        <div class="ibox-content">
                                <table class="footable table table-stripped toggle-arrow-tiny table-hover table-order">
                                    <thead>
                                    <tr>
                                        <th class="text-uppercase">Kiện hàng</th>
                                        <th class="text-uppercase">Vận đơn</th>
                                        <th class="text-uppercase">Trạng thái</th>
                                        <th class="text-uppercase">Thời gian</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($packages as $package)
                                        <tr>
                                            <td data-value="{{$package->logistic_package_barcode}}">
                                                {{$package->logistic_package_barcode}}

                                                @if($package->weight)
                                                    <br>
                                                    <small>
                                                        {{ $package->getWeightCalFee() }} kg
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                {{$package->freight_bill}}
                                            </td>
                                            <td data-value="{{ App\Package::getStatusTitle($package->status)  }}">
                                                <span class=" label label-success ">
                                                    {{ App\Package::getStatusTitle($package->status)  }}
                                                </span>
                                                <br>

                                                <small>
                                                    @if($package->current_warehouse)
                                                        Kho hiện tại: {{$package->current_warehouse}} <br>
                                                    @endif

                                                    @if($package->warehouse_status)
                                                        Tình trạng: {{ App\Package::getWarehouseStatusName($package->warehouse_status) }}
                                                        @if($package->warehouse_status == App\Package::WAREHOUSE_STATUS_IN)
                                                            ({{ App\Util::formatDate($package->warehouse_status_in_at)}})
                                                        @endif

                                                        @if($package->warehouse_status == App\Package::WAREHOUSE_STATUS_OUT)
                                                            ({{ App\Util::formatDate($package->warehouse_status_out_at)}})
                                                        @endif
                                                    @endif

                                                </small>
                                            </td>

                                            <td data-value="{{$package->created_at}}">{{  App\Util::formatDate($package->created_at)}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('sidebar')
    <div id="right-sidebar">
        <div class="sidebar-container" id="anchor-box-comment">
            @include('onipartials/__comment', [
                'object_id' => $order_id,
                'object_type' => App\Comment::TYPE_OBJECT_ORDER,
                'scope_view' => App\Comment::TYPE_EXTERNAL
            ])
        </div>
    </div>
@endsection

@section('header-scripts')
    <link href="{!! asset('oniasset/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') !!}" rel="stylesheet"/>
    <link href="{!! asset('oniasset/css/plugins/footable/footable.core.css') !!}" rel="stylesheet"/>
@endsection

@section('footer-scripts')
    <script src="{!! asset('oniasset/js/plugins/slimscroll/jquery.slimscroll.min.js') !!}"></script>
    <script src="{{ asset('oniasset/js/plugins/footable/footable.all.min.js') }}"></script>

    <script>
    $(document).ready(function() {
        // Initialize slimscroll for right sidebar
        $('#anchor-box-comment').slimScroll({
        height: '100%',
        railOpacity: 0.4,
        wheelStep: 10
        });

        $('.footable').footable();
    });
    </script>
@endsection

