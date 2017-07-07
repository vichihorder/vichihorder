<p>Tìm thấy {{ $total_orders }} đơn hàng</p>

@if(count($orders))

    <div class="table-responsive">

        <table class="table table-striped table-hover no-padding-leftright">
            <thead>
            <tr>

                <th width="25%">Đơn hàng</th>
                <th width="20%">Nhân viên</th>
                <th width="35%">Phí trên đơn</th>
                <th width="20%">Thời gian</th>
            </tr>
            </thead>
            <tbody>

            @foreach($orders as $order)
                <tr>

                    <td>

                        <img
                                class="lazy"
                                data-src="{{$order->avatar}}"
                                alt="" style="float: left; margin-right: 10px;" width="100px">

                        <div>
                            {!! App\Util::showSite($order->site) !!}
                            <br>

                            <a href="{{ url('order', $order->id)  }}" title="{{$order->code}}">{{$order->code}}</a>

                            <p>
                                {{ App\Order::getStatusTitle($order->status)  }}
                                <br>
                                @if(isset($services[$order->id]))
                                    @foreach($services[$order->id] as $service)

                                        <span data-toggle="tooltip"
                                              title="{{ $service['name']  }}"
                                              class=""
                                              data-code="{{ $service['code']  }}">

                                                        <i class="fa {{ $service['icon']  }}"></i>

                                                    </span>

                                    @endforeach
                                @endif
                            </p>
                        </div>


                    </td>

                    <td>
                        <h5>KHÁCH HÀNG</h5>
                        <?php
                        $customer = App\User::find($order->user_id);
                        echo '<p><a href="' . url('user/detail', $customer->id) . '">' . $customer->email . '</a> ('. $customer->code .')</p>';
                        ?>

                        <p>
                            Đặt cọc ({{$order->deposit_percent}}%): <span class="text-danger">{{ App\Util::formatNumber($order->deposit_amount) }} <sup>đ</sup></span>
                        </p>
                        <p>
                            Số dư: <span class="text-danger">{{App\Util::formatNumber($order->customer->account_balance)}} <sup>đ</sup></span>

                        </p>



                        @if($order->paid_staff)
                            <hr>
                            <h5>MUA HÀNG</h5>
                        <p>
                            <a href="{{  url('user/detail', $order->paid_staff->id) }}">{{$order->paid_staff->email}}</a> ({{$order->paid_staff->code}})
                        </p>



                        @else

                            @if($can_set_crane_buying)

                            <hr>
                            <h5>MUA HÀNG</h5>
                            <p>
                                <select style="width: 200px;" class="_crane_staff_buying" name="crane_staff_buying" id="">
                                    <option

                                            data-order-id="{{$order->id}}"
                                            value="">--Chọn nhân viên mua hàng--</option>

                                    @foreach($crane_buying_list as $crane_buying_list_item)
                                        <option
                                                @if($order->crane_staff_id
                                                && $order->crane_staff_id == $crane_buying_list_item->id)
                                                 selected
                                                @endif


                                                data-order-id="{{$order->id}}"

                                                value="{{$crane_buying_list_item->id}}">{{$crane_buying_list_item->name}} - {{$crane_buying_list_item->email}} - {{$crane_buying_list_item->code}}</option>
                                    @endforeach
                                </select>
                            </p>

                            @endif
                        @endif

                        <p>
                            <a class="_export_excel" id="_export_excel_{{$order->id}}" href="{{ url('export-excel-finance?order_id=') }}{{$order->id}}" data-order-id="{{$order->id}}">Xuất excel tài chính</a>
                        </p>
                    </td>

                    <td>
                        {{--<span class="text-danger">{{ App\Util::formatNumber($order->amount * $order->exchange_rate) }} <sup>đ</sup></span>--}}

                        <small>
                            @foreach($order->order_fee as $order_fee_item)
                                <div style="width: 50%; display: inline-block; float: left;">
                                    <div style="padding: 5px 0;">
                                        {!! $order_fee_item['label'] !!}:
                                        <span class="text-danger">
                                                                <strong>{{$order_fee_item['value']}}<sup>đ</sup></strong>
                                                            </span>
                                    </div>
                                </div>
                            @endforeach
                        </small>


                    </td>
                    <td>
                        <small>
                            <?php
                            foreach(App\Order::$timeListOrderDetail as $k => $v){
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

    </div>

    {{ $orders->appends(request()->input())->links() }}
@else
    <h4>Hiện chưa có đơn hàng!</h4>
@endif

