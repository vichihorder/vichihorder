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
                                        ['name' => 'Thống kê', 'link' => null],
                                    ]
                                ]
                            )

                <div class="card-body">
                    <form action="{{ url('accouting_finance') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select
                                            data-live-search="true"
                                            class="form-control _selectpicker" name="user_id" id="">
                                        <option value="">Khách hàng</option>
                                        <?php
                                        $customer = App\User::findBySection(App\User::SECTION_CUSTOMER);
                                        foreach($customer as $customer_item){
                                            $selected = $customer_item->id == request()->get('user_id') ? ' selected ' : '';

                                            echo '<option ' . $selected . ' value="' . $customer_item->id . '">' . $customer_item->name . ' - ' . $customer_item->email . ' - ' . $customer_item->code . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 hidden">
                                <div class="form-group">
                                    <select class="form-control" name="order_status">
                                        <option value="0">Tất cả các trạng thái</option>
                                        @foreach($order_status as $item_status)
                                            <option value="{{$item_status}}">{{\App\Order::$statusTitle[$item_status]}}</option>
                                         @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 hidden">
                                <?php


                                $date_from = empty(request()->get('time_from')) ? date('Y-m-d') : request()->get('time_from');
                                $date_to = empty(request()->get('time_to')) ? date('Y-m-d') : request()->get('time_to');



                                ?>
                                Từ: {{ Form::text('time_from', $date_from, array('id' => 'datepicker') )}}
                            </div>
                            {{--<div class="col-md-3">
                                Đến: {{ Form::text('time_to', $date_to, array('id' => 'datepicker2') )}}
                            </div>--}}

                            <div class="col-md-2">

                                <button type="submit" class="btn btn-primary"

                                >Xuất Khách Nợ</button>
                            </div>

                        </div>



                        <br>
                        {{--<div class="row">--}}
                                    {{--<div class="col-md-2">--}}

                                        {{--<button type="submit" class="btn btn-primary"--}}

                                        {{-->Xuất Khách Nợ</button>--}}
                                    {{--</div>--}}
                        {{--</div>--}}

                    </form>

                </div>
            </div>
        </div>
    </div>


@endsection

@section('js_bottom')
    @parent
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.lazy.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-select.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('._selectpicker').selectpicker({
//                style: 'btn-info',
//                width: 'fit',
            });
        });
        $(function () {
            $("#datepicker").datepicker({dateFormat: 'yy-mm-dd'});
            $("#datepicker2").datepicker({dateFormat: 'yy-mm-dd'});
        });
    </script>

@endsection

@section('css_bottom')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-select.min.css') }}">
@endsection