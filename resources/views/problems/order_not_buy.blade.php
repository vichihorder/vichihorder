<?php
//Đơn hàng quá ? ngày chưa mua hàng kể từ khi khách đặt coc đơn
$l_time = strtotime("-{$long_time} day", strtotime(date('Y-m-d H:i:s')));
$l_time = date('Y-m-d H:i:s', $l_time);

$orders = \Illuminate\Support\Facades\DB::select("
            select * from `order` where `status` = '".App\Order::STATUS_DEPOSITED."' and `deposited_at` <= '".$l_time."'
        ");
if(count($orders)){
?>

<table class="table">
    <thead>
    <tr>
        <th>TT</th>
        <th>Đơn hàng</th>
        <th>Đặt cọc lúc</th>
        <th>Khách</th>
        <th>NV đuợc phân đơn</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach($orders as $idx => $order){
    $customer = App\User::find($order->user_id);
    $crane_staff = App\User::find($order->crane_staff_id);
    ?>
    <tr>
        <td>{{$idx+1}}</td>
        <td>
            <a href="{{ url('order/detail', $order->code)}}">{{$order->code}}</a>
            <small>{{ App\Order::getStatusTitle($order->status) }}</small>
        </td>
        <td>
            {{App\Util::formatDate($order->deposited_at)}}

            <small>(<abbr title="{{$order->deposited_at}}" class="_time-ago"></abbr>)</small>
        </td>
        <td>
            <a href="{{url('user/detail', $customer->id)}}">{{$customer->name}}</a> <small>{{$customer->code}}</small>
        </td>
        <td>
            <?php if($crane_staff instanceof App\User){ ?>
                <a href="{{url('user/detail', $crane_staff->id)}}">{{$crane_staff->name}}</a> <small>{{$crane_staff->code}}</small>
            <?php }else{ ?>
                <a href="{{ url('order?order_code=' . $order->code)  }}">>> Phân đơn</a>
            <?php } ?>
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