@extends('layouts.app')

@section('page_title')
    {{@$page_title}}
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
                    <h3 class="card-title">{{@$page_title}}</h3>

                    <p>Tìm thấy {{$total_users}} khách hàng</p>

                    @if($total_users > 0)

                        <table class="table table-striped table-hover no-padding-leftright">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th class="text-right">Nạp tiền</th>
                                    <th class="text-right">Tiền hàng(1)</th>
                                    <th class="text-right">Đặt cọc(2)</th>
                                    <th class="text-right">Còn thiếu (3=1-2)</th>
                                    <th class="text-right">Số dư</th>
                                </tr>
                            </thead>
                            <tbody>

                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <h4>
                                            <a href="{{ url('user/detail', $user->id)  }}">{{$user->email}}</a>
                                        </h4>
                                        <p>
                                            Họ & Tên: {{$user->name}}
                                        </p>
                                        <p class="">Mã: <span class="text-danger">{{$user->code}}</span></p>
                                    </td>
                                    <td class="text-right">
                                        <span class="text-danger">{{ App\Util::formatNumber($user->input_money_vnd)  }}đ</span>
                                    </td>
                                    <td class="text-right">
                                        <span class="text-danger">{{ App\Util::formatNumber($user->amount_vnd)  }}đ</span>
                                    </td>
                                    <td class="text-right">
                                        <span class="text-danger">{{ App\Util::formatNumber($user->deposit_vnd)  }}đ</span>
                                    </td>
                                    <td class="text-right">
                                        <span class="text-danger">{{ App\Util::formatNumber($user->need_payment_vnd)  }}đ</span>
                                    </td>
                                    <td class="text-right">
                                        <span class="text-danger">
                                            {{ App\Util::formatNumber($user->account_balance)  }}đ
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    @else
                        <p>Hiện chưa có khách hàng nào!</p>
                    @endif


                </div>
            </div>
        </div>
    </div>

@endsection

@section('js_bottom')
    @parent
    <script>
        $(document).ready(function(){


        });

    </script>
@endsection

