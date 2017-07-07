<?php

$steps = [
    [
        'title' => 'Giỏ hàng ',
        'icon' => 'fa fa-shopping-basket'
    ],
    [
        'title' => 'Đặt cọc & Thanh toán ',
        'icon' => 'fa fa-money'
    ],
    [
        'title' => 'NM247 tiếp nhận & xử lý ',
        'icon' => 'fa fa-truck'
    ],
    [
        'title' => 'Nhận hàng ',
        'icon' => 'fa fa-home'
    ],
];

?>

<!-- success, select, normal -->
<div class="steps">
    @foreach($steps as $idx => $step)
    <?php
        if($status[$idx] == '2')
            $status_class = 'success';
        else if($status[$idx] == '1')
            $status_class = 'select';
        else
            $status_class = '';
        ?>
    <div class="step-item {{$status_class}}">
        <div class="step-icon">
            <i class="{{$step['icon']}}" aria-hidden="true"></i>
        </div>
        <div class="step-node">
            <i class="node-icon"></i>
        </div>
        <div class="step-title">
            {{$step['title']}}
        </div>
    </div>
    @endforeach
</div>