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
                    <h3>{{$page_title}}</h3>


                    <p>
                        Tìm thấy {{$total_bill_manage}} phiếu
                    </p>

                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>TT</th>
                                <th>Mã</th>
                                <th>NV Tạo</th>
                                <th>Khách</th>
                                <th>Đơn</th>
                                <th>Kiện</th>
                                <th>Thu hộ</th>
                                <th>Ship nội dịa</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>

                            @if(count($bill_mange_list))
                                @foreach($bill_mange_list as $idx => $bill_mange_item)
                                    <tr>
                                        <td>{{$idx+1}}</td>
                                        <td>
                                            <a href="{{ url('BillManage/Detail',$bill_mange_item->id)  }}">
                                                {{$bill_mange_item->code}}
                                            </a>
                                        </td>
                                        <td>
                                            <strong>
                                                <a href="{{ url('user/detail', $bill_mange_item->create_user_object->id)  }}">
                                                    {{$bill_mange_item->create_user_object->name}}
                                                </a>
                                            </strong>
                                            <p>{{$bill_mange_item->create_user_object->code}}</p>
                                        </td>
                                        <td>
                                            <strong>
                                                <a href="{{ url('user/detail', $bill_mange_item->buyer_object->id)  }}">
                                                    {{$bill_mange_item->buyer_object->name}}
                                                </a>
                                            </strong>
                                            <p>{{$bill_mange_item->buyer_object->code}}</p>
                                        </td>
                                        <td>

                                            {!! $bill_mange_item->orders_links !!}

                                        </td>
                                        <td>

                                            {!! $bill_mange_item->packages_links !!}

                                        </td>
                                        <td>
                                            {{App\Util::formatNumber($bill_mange_item->amount_cod)}} đ
                                        </td>
                                        <td>
                                            {{App\Util::formatNumber($bill_mange_item->domestic_shipping_vietnam)}} đ
                                        </td>
                                        <td>
                                            <small>
                                                <p><a href="{{ url('BillManage/Print', $bill_mange_item->id)  }}" target="_blank">In phiếu</a></p>
                                                <p><a href="{{ url('BillManage/Detail', $bill_mange_item->id)  }}">Xem chi tiết</a></p>
                                            </small>

                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                        </tbody>
                    </table>



                    {{ $bill_mange->appends(request()->input())->links() }}

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

