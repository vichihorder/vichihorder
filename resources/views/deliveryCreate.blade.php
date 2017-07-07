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
                            ['name' => 'Yêu cầu giao hàng', 'link' => url('DeliveryManage')],
                            ['name' => 'Tạo phiếu giao', 'link' => null],
                        ]
                    ]
                )
                <div class="card-body">
                    <h3 style="margin-top: 0; margin-bottom: 30px;">{{$page_title}}</h3>

                    <h4>
                        <a href="{{ url('user/detail', $customer->id)  }}">{{$customer->name}}</a>
                        <small> · {{$customer->email}}</small>
                        <small> · {{$customer->code}}</small></h4>

                    <p class="@if($customer->account_balance > 0) text-success @else text-danger @endif">
                        Số dư: {{App\Util::formatNumber($customer->account_balance)}} đ
                        ·
                        @if($customer->account_balance > 0) Tài chính đủ @else Tài chính thiếu @endif
                    </p>

                    <div>
                        <i class="fa fa-user"></i>
                        {{$user_address_detail->reciver_name}},

                        <i class="fa fa-phone"></i>
                        {{$user_address_detail->reciver_phone}},

                        <i class="fa fa fa-map-marker"></i>
                        {{$user_address_detail->detail}},
                        {{$user_address_detail->district->label}},
                        {{$user_address_detail->province->label}}
                    </div>

                    <h6>
                        Tổng {{$total_orders}} đơn, {{count($packages_list)}} kiện
                    </h6>

                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="_chk-all" checked>
                                </th>
                                <th>TT</th>
                                <th>Đơn hàng</th>
                                <th>Kiện hàng</th>
                                <th>Kho hiện tại</th>
                                <th>Cân nặng tinh phí</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if(count($packages_list))
                                @foreach($packages_list as $idx => $packages_list_item)
                                    <tr class="_package"

                                        data-current-warehouse="{{ $packages_list_item->current_warehouse }}"

                                        data-logistic-package-barcode="{{$packages_list_item->logistic_package_barcode}}">
                                        <td>
                                            <input
{{--                                                    @if($packages_list_item->warehouse_status == 'IN') disabled @endif--}}
type="checkbox"

checked

data-logistic-package-barcode="{{$packages_list_item->logistic_package_barcode}}"
data-order-code="{{$packages_list_item->order->code}}"

class="_chk">
                                        </td>
                                        <td>{{$idx+1}}</td>
                                        <td>
                                            <a href="{{ url('order/detail', $packages_list_item->order->id)  }}">{{$packages_list_item->order->code}}</a>
                                        </td>
                                        <td>

                                            <a href="{{ url('package', $packages_list_item->logistic_package_barcode)  }}">{{$packages_list_item->logistic_package_barcode}}</a>
                                            <br>( <span class="_package-status">{{ App\Package::getStatusTitle($packages_list_item->status)  }}</span> )

                                            {{--<br>--}}
                                            {{--<button class="_action-out-warehouse btn-sm">XUẤT KHO</button>--}}
                                        </td>
                                        <td>

                                            {{$packages_list_item->current_warehouse}} <br>


                                            @if($packages_list_item->warehouse_status == App\Package::WAREHOUSE_STATUS_IN)
                                                (Nhập kho: {{  App\Util::formatDate($packages_list_item->warehouse_status_in_at) }})
                                            @endif

                                            @if($packages_list_item->warehouse_status == App\Package::WAREHOUSE_STATUS_OUT)
                                                (Xuất kho: {{  App\Util::formatDate($packages_list_item->warehouse_status_out_at) }})
                                            @endif
                                        </td>
                                        <td>
                                            <p>Tịnh: {{$packages_list_item->weight}} kg</p>
                                            <p>Quy đổi: {{(int)$packages_list_item->converted_weight}} kg</p>
                                            <p>Tính phí: {{ $packages_list_item->weight > (int)$packages_list_item->converted_weight ? $packages_list_item->weight : (int)$packages_list_item->converted_weight }} kg</p>

                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>


                    Tiền vận chuyển nội địa:
                    <input type="number" name="domestic_shipping_vietnam" value="0" class="_domestic_shipping_vietnam text-right"> đ
                    Tiền thu hộ
                    <input type="number" name="amount_cod" value="0" class="_amount_cod text-right"> đ

                    <button style="margin-left: 50px;" class="btn btn-danger text-uppercase" id="_create-sheet">Tạo phiếu giao</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js_bottom')
    @parent
    <script>
        $(document).ready(function(){

            $(document).on('click', '#_create-sheet', function(){
                 var $that = $(this);
                 var len = $('._chk:checked').length;
                 if(!len){
                     alert('Vui lòng chọn kiện hàng để tạo phiếu giao');
                     return false;
                 }

                 $that.prop('disabled', true);

                 var domestic_shipping_vietnam = $('._domestic_shipping_vietnam').val();
                 var amount_cod = $('._amount_cod').val();
                 var packages = [];
                 var orders = [];

                 $('._chk:checked').each(function(i){
                    packages.push($(this).data('logistic-package-barcode'));
                    orders.push($(this).data('order-code'));
                 });

                 request("{{ url('BillManage/Create')  }}", "post", {
                     domestic_shipping_vietnam:domestic_shipping_vietnam,
                     amount_cod:amount_cod,
                     packages:packages,
                     orders:orders,
                     buyer_id:"{{request()->get('user_id')}}",
                     buyer_address_id:"{{ request()->get('user_address_id') }}",
                     _token:"{{csrf_token()}}"

                 }).done(function(response){
                        if(response.success){

                            window.location = response.url;

                        } else{
                            bootbox.alert(response.message);
                            $that.prop('disabled', false);
                        }
                 });

            });

            $(document).on('click', '._action-out-warehouse', function(){
                var $that = $(this);
                $that.prop('disabled', true);
                var $row = $(this).parents('._package');

                var logistic_package_barcode = $row.data('logistic-package-barcode');
                var current_warehouse = $row.data('current-warehouse');
                request("{{ url('scan/action')  }}", 'post', {
                    action:'OUT',
                    warehouse:current_warehouse,
                    _token:"{{csrf_token()}}",
                    barcode:logistic_package_barcode
                }).done(function(response){
                     if(response.success){
                         $that.text('ĐÃ XUẤT KHO');
                         $row.find('._package-status').text('Đang giao hàng');
                         $row.find('._chk').prop('disabled', false);
                     }else{
                         bootbox.alert(response.message);
                         $that.prop('disabled', false);
                     }
                });
            });

            $(document).on('click', '._chk-all', function(){
                $('._chk:not(:disabled)').prop('checked', $(this).prop('checked'));
            });

        });

    </script>
@endsection

