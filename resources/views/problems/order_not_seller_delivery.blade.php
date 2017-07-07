<div class="row">

    <div class="col-sm-3">
        <select
                class="form-control _selectpicker _paid_staff_id"
                name="paid_staff_id">
            <option value="">Nhân viên mua hàng</option>
            @foreach($crane_buying_list as $crane_buying_list_item)
                <option

                        @if( request()->get('paid_staff_id') == $crane_buying_list_item->id )
                        selected
                        @endif

                        value="{{$crane_buying_list_item->id}}">{{$crane_buying_list_item->name}} - {{$crane_buying_list_item->email}} - {{$crane_buying_list_item->code}}</option>
            @endforeach
        </select>
    </div>

</div>


<?php
//Đơn quá ? ngày người bán chưa phát hàng kể từ khi mua hàng
$l_time = strtotime("-{$long_time} day", strtotime(date('Y-m-d H:i:s')));
$l_time = date('Y-m-d H:i:s', $l_time);


$and_where = "";
if($crane_buying_selected){
    $and_where .= " and `paid_staff_id` = {$crane_buying_selected} ";
}
$orders = \Illuminate\Support\Facades\DB::select("
            select * from `order` where `status` = '".App\Order::STATUS_BOUGHT."' {$and_where} and `bought_at` <= '".$l_time."'
        ");
if(count($orders)){
?>

<table class="table">
    <thead>
    <tr>
        <th>TT</th>
        <th>Đơn hàng</th>
        <th>Mua hàng lúc</th>
        <th>Khách</th>
        <th>NV Mua hàng</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach($orders as $idx => $order){
    $customer = App\User::find($order->user_id);
    $crane_buying = App\User::find($order->paid_staff_id);
    ?>
    <tr>
        <td>{{$idx+1}}</td>
        <td>
            <a href="{{ url('order/detail', $order->code)}}">{{$order->code}}</a>
            <small>{{ App\Order::getStatusTitle($order->status) }}</small>
        </td>
        <td>
            {{App\Util::formatDate($order->bought_at)}}
            <small>(<abbr title="{{$order->bought_at}}" class="_time-ago"></abbr>)</small>
        </td>
        <td>
            <a href="{{url('user/detail', $customer->id)}}">{{$customer->name}}</a> <small>{{$customer->code}}</small>
        </td>
        <td>
            <a href="{{url('user/detail', $crane_buying->id)}}">{{$crane_buying->name}}</a> <small>{{$crane_buying->code}}</small>
        </td>
    </tr>

    <?php
    }
    ?>
    </tbody>
</table>

<?php
}
?>