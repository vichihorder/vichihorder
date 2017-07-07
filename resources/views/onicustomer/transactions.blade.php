@extends($layout)

@section('page_title')
    {{$page_title}}
@endsection

@section('content')
        <div class="ibox">
            <div class="ibox-content">
                <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="15">
                    <thead>
                    <tr>
                        <th>Mã GD</th>
                        <th data-hide="phone" width="120">Loại</th>
                        <th data-hide="phone" class="text-center" width="120">Trạng thái</th>
                        <th data-hide="phone" class="text-center">Đối tượng</th>
                        <th data-hide="phone" class="text-center" width="120">Thời gian</th>
                        <th class="text-right" width="120">Giá trị </th>
                        <th class="text-right" width="120">Số dư cuối </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transactions as $transaction)
                        <?php
                        $user = App\User::find($transaction->user_id);
                        $order = App\Order::find($transaction->object_id);

                        if(!$user) $user = new App\User();
                        if(!$order) $order = new App\Order();
                        ?>
                        <tr>
                            <td>
                                {{$transaction->transaction_code}}<br>
                                <small class="" style="color: grey">{{$transaction->transaction_note}}</small>
                            </td>
                            <td>
                                {{ App\UserTransaction::$transaction_type[$transaction->transaction_type]  }}
                            </td>
                            <td class="text-center">
                                <span class="@if($transaction->state == App\UserTransaction::STATE_COMPLETED) label label-success @endif">
                                    {{ App\UserTransaction::$transaction_state[$transaction->state]  }}
                                </span>
                            </td>
                            <td>
                                @if($transaction->object_type == App\UserTransaction::OBJECT_TYPE_ORDER)
                                    <a href="{{ url('don-hang', $order->id) }}">{{$order->code}}</a>
                                @endif
                            </td>
                            <td class="text-center" data-value="{{$transaction->created_at}}">
                                {{ App\Util::formatDate($transaction->created_at)  }}
                            </td>
                            <td class="text-right" data-value="{{$transaction->amount}}">
                                <span class="text-danger">
                                    {{ App\Util::formatNumber($transaction->amount) }} <sup>d</sup>
                                </span>
                            </td>
                            <td class="text-right" data-value="{{$transaction->ending_balance}}">
                                <strong>
                                    {{ App\Util::formatNumber($transaction->ending_balance) }} <sup>d</sup>
                                </strong>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="7">
                            <ul class="pagination pull-right"></ul>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
@endsection

@section('header-scripts')
    <link href="{!! asset('oniasset/css/plugins/footable/footable.core.css') !!}" rel="stylesheet"/>
@endsection

@section('footer-scripts')
    <script src="{{ asset('oniasset/js/plugins/footable/footable.all.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.footable').footable();
        });
    </script>
@endsection

