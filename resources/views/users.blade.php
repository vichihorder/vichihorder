@extends('layouts.app')
{{--@extends('layouts.app_blank')--}}

@section('page_title')
    {{$page_title}}
@endsection

@section('content')

    <div class="row">
        <div class="col-xs-12">
            <div class="card">

                @include('partials/__breadcrumb',
                                [
                                    'urls' => [
                                        ['name' => 'Trang chủ', 'link' => $app->make('url')->to('home')],
                                        ['name' => 'Nhân viên', 'link' => null],
                                    ]
                                ]
                            )

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">

                            <form onchange="this.submit();" class="form-inline" method="get" action="{{ url('user')  }}">

                                <div class="row">
                                    <div class="col-sm-3 col-xs-12">
                                        {{--<input--}}
                                                {{--class="form-control"--}}
                                                {{--value="{{@$condition['code']}}" placeholder="Mã NV" autofocus type="text" name="code">--}}


                                        <select
                                                data-live-search="true"
                                                class="form-control _selectpicker" name="customer_code_email" id="">
                                            <option value="">Khách hàng</option>
                                            <?php
                                            $customer = App\User::findBySection(App\User::SECTION_CUSTOMER);
                                            foreach($customer as $customer_item){
                                                $selected = $customer_item->id == request()->get('customer_code_email') ? ' selected ' : '';

                                                echo '<option ' . $selected . ' value="' . $customer_item->id . '">' . $customer_item->name . ' - ' . $customer_item->email . ' - ' . $customer_item->code . '</option>';
                                            }
                                            ?>
                                        </select>


                                    </div>

                                    {{--<div class="col-sm-3 col-xs-12">--}}
                                        {{--<input--}}
                                                {{--class="form-control"--}}
                                                {{--value="{{@$condition['email']}}" placeholder="Email" type="text" name="email">--}}
                                    {{--</div>--}}
                                    <div class="col-sm-3 col-xs-12">
                                        <select class="form-control _selectpicker" name="section" id="">
                                            <option value="">Đối tượng</option>
                                            @foreach(App\User::$section_list as $k => $v)
                                                <option

                                                        @if(isset($condition['section']) && $k == $condition['section'])
                                                        selected
                                                        @endif

                                                        value="{{$k}}">{{$v}}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="col-sm-3 col-xs-12">
                                        <select class="form-control _selectpicker" name="status" id="">
                                            <option value="">Trạng thái</option>
                                            @foreach(App\User::$status_list as $k => $v)
                                                <option

                                                        @if(isset($condition['status']) && $k == $condition['status'])
                                                        selected
                                                        @endif

                                                        value="{{$k}}">{{$v}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-3 col-xs-12">
                                        <button type="submit" class="btn btn-danger">Tìm kiếm</button>
                                    </div>
                                </div>



                                <div class="row">

                                </div>

                            </form>


                            <br>

                            <p>
                                Tìm thấy ({{ $total_users }}) nhân viên
                            </p>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Đối tượng</th>
                                        <th>Họ & tên</th>
                                        <th>Trạng thái</th>
                                        <th>Thời gian</th>
                                        <th>Số dư cuối</th>
                                    </tr>
                                </thead>
                                <tbody>

                                @if(!empty($users))
                                    @foreach($users as $key => $user)
                                        <tr>
                                            <td>{{$user->id}}</td>
                                            <td>{{ App\User::getSectionName($user->section)  }}</td>
                                            <td>
                                                <a href="{{ url('user/detail', $user->id)  }}">{{$user->email}}</a>

                                                ({{$user->code}})

                                                <br>
                                                <small>{{$user->name}}</small>
                                                <br>

                                                @if($can_view_cart_customer)
                                                <small>
                                                    <a href="{{ url('gio-hang?hosivan_user_id=' . $user->id)  }}">Xem giỏ hàng</a>
                                                </small>
                                                @endif

                                                <br>
                                                <small>
                                                    <a href="{{ url('order?customer_code_email=' . $user->code)  }}">Xem đơn hàng</a>
                                                </small>
                                            </td>
                                            <td>{{ App\User::getStatusName($user->status)  }}</td>
                                            <td>
                                                Gia nhập: {{ date('H:i d/m/Y', strtotime($user->created_at)) }}
                                            </td>
                                            <td class="text-right">
                                                {{ App\Util::formatNumber($user->account_balance)  }} <sup>đ</sup>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                                </tbody>
                            </table>

{{--                            {{ $users->links() }}--}}

                            {{ $users->appends(request()->input())->links() }}

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('js_bottom')
    @parent

    <script type="text/javascript" src="{{ asset('js/bootstrap-select.min.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('._selectpicker').selectpicker({
//                style: 'btn-info',
//                width: 'fit',
            });
        })
    </script>
@endsection

@section('css_bottom')
@parent
<link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-select.min.css') }}">
<style>
    .form-control{
        width: 100%!important;
    }
</style>

@endsection

