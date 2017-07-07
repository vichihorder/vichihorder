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

                    <h3 style="margin-top: 0">{{$page_title}}</h3>

                    @if($customers)
                        @foreach($customers as $customer)
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="_row @if($customer->account_balance < 0) warn @endif" >
                                        <h4>
                                            <a href="{{ url('user/detail', $customer->id)  }}">{{$customer->name}}</a>
                                            ·

                                            <small>{{$customer->email}}</small>

                                            ·
                                            <small>Mã: {{$customer->code}}</small>

                                            ·
                                            <small class="account_balance">Số dư: {{App\Util::formatNumber($customer->account_balance)}}đ</small>
                                        </h4>


                                        @if(isset($customer_user_address[$customer->id]))
                                            <ul style="list-style: none;">
                                                @foreach($customer_user_address[$customer->id] as $customer_user_address_item)
                                                    <li>
                                                        <i class="fa fa-user"></i>
                                                        {{$customer_user_address_item->reciver_name}},

                                                        <i class="fa fa-phone"></i>
                                                        {{$customer_user_address_item->reciver_phone}},

                                                        <i class="fa fa fa-map-marker"></i>
                                                        {{$customer_user_address_item->detail}},
                                                        {{$customer_user_address_item->district->label}},
                                                        {{$customer_user_address_item->province->label}}

                                                        (<i>{{$customer_user_address_item->total_package_waiting_delivery}} kiện chờ giao</i>)

                                                        ·
                                                        <a href="{{url(sprintf('DeliveryManage/Create?user_id=%s&user_address_id=%s', $customer_user_address_item->user_id, $customer_user_address_item->id))}}">Tạo phiếu giao</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>




                                </div>
                            </div>

                        @endforeach

                    @endif

                </div>
            </div>
        </div>
    </div>

@endsection

@section('css_bottom')
    <style>
        .warn{
            background: rgba(255, 255, 0, 0.24);
        }
        .warn .account_balance{
            color: darkred;
        }
        ._row{
            border-bottom: 1px solid #ccc;
            padding-bottom: 15px;
            padding-top: 15px;
            padding-left: 10px;
            padding-right: 10px;
        }
    </style>
@endsection

@section('js_bottom')
    @parent
    <script>
        $(document).ready(function(){


        });

    </script>
@endsection

