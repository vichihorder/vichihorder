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
                            ['name' => 'Phiếu giao hàng', 'link' => url('BillManage')],
                            ['name' => $page_title, 'link' => null],
                        ]
                    ]
                )
                <div class="card-body">
                    <h3>{{$page_title}}</h3>


                    <ul>
                        <li><h4>Khách hàng:

                                <a href="{{ url('user/detail', $bill->buyer_object->id)  }}">{{$bill->buyer_object->name}} </a> <small>· {{$bill->buyer_object->email}} · {{$bill->buyer_object->code}}</small>
                            </h4></li>
                        <li>Mã phiếu: #{{$bill->code}}</li>
                        <li>NV tạo:

                            <a href="{{ url('user/detail', $bill->create_user_object->id)  }}">{{$bill->create_user_object->name}} </a> · {{$bill->create_user_object->email}} · {{$bill->create_user_object->code}}

                        </li>
                        <li>Thu hộ: <span class="_panel">{{ $bill->amount_cod  }} đ</span> <a href="javascript:void(0)" data-type="amount_cod" class="_edit-fee">Sửa</a></li>
                        <li>Phí ship nội địa: <span class="_panel">{{ $bill->domestic_shipping_vietnam  }} đ</span> <a href="javascript:void(0)" data-type="domestic_shipping_vietnam" class="_edit-fee">Sửa</a></li>
                        <li>Đơn hàng: {!! $bill->orders_links !!}</li>
                        <li>Kiện hàng: {!! $bill->packages_links !!}</li>
                        <li>Thông tin nhận hàng:

                            <i class="fa fa-user"></i>
                            {{$bill->buyer_address->reciver_name}},

                            <i class="fa fa-phone"></i>
                            {{$bill->buyer_address->reciver_phone}},

                            <i class="fa fa fa-map-marker"></i>
                            {{$bill->buyer_address->detail}},
                            {{$bill->buyer_address->district->label}},
                            {{$bill->buyer_address->province->label}}

                        </li>
                        <li>Tạo: {{App\Util::formatDate($bill->created_at)}}</li>

                        @if($bill->updated_at)
                        <li>Sửa: {{App\Util::formatDate($bill->updated_at)}}</li>
                        @endif
                    </ul>

                    <a href="{{ url('BillManage/Print', $bill->id)  }}" target="_blank">In phiếu</a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js_bottom')
    @parent
    <script>
        $(document).ready(function(){

            var data = {
                amount_cod:"{{$bill->amount_cod}}",
                domestic_shipping_vietnam:"{{$bill->domestic_shipping_vietnam}}"
            };

            $(document).on('click', '._edit-fee', function(){
                var type = $(this).data('type');
                var amount = $(this).prev().find('._form-edit-fee').val();
                var $that = $(this);

                if($that.hasClass('disabled')) return false;

                $that.addClass('disabled');

                var text = $(this).text();
                if(text == 'Lưu'){
                    request("{{ url('BillManage/UpdateFee')  }}", "post", {
                        bill_id:"{{$bill->id}}",
                        amount:amount,
                        type:type
                    }).done(function(response){
                        if(response.success){
                            data[type] = amount;
                            $that.prev().text(data[type] + ' đ');
                            $that.text('Sửa');
                        } else{
                            bootbox.alert(response.message);
                        }
                        $that.removeClass('disabled');
                    });

                }else{
                    $that.removeClass('disabled');
                    $(this).text('Lưu');
                    $(this).prev().html('<input type="number" class="_form-edit-fee" value="' + data[type] + '" />');
                    $(this).prev().find('._form-edit-fee').focus();
                }
            });

        });

    </script>
@endsection

