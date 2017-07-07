@extends('layouts.app')

@section('page_title')
    {{$page_title}}
@endsection

@section('content')
    <table class="table table-hover table-bordered">
        <thead>
        <tr>

            <th>Khiếu nại</th>
            <th>Mã đơn</th>
            <th>Trạng thái</th>
            <th>Thời gian tạo</th>
            <th>Chi tiết</th>
        </tr>
        </thead>
        <tbody>
        @if(!empty($data))
            <?php $i = 1; ?>
            @foreach($data as $complaint)

                    <tr>
                        <td>{{ $complaint->title }}</td>
                        <td>{{ App\Complaints::getOrderCode($complaint->order_id) }}</td>
                        <td>{{ App\Complaints::$alias_array[$complaint->status] }}</td>
                        <td>{{ $complaint->created_time }}</td>
                        <td> <a target="_blank" href="/chi-tiet-khieu-nai/{{$complaint->id}}">chi tiết <i class="fa fa-angle-right"></i></a></td>
                    </tr>

            @endforeach
        @endif
        </tbody>
    </table>
    @if(!empty($data))

    @else
        <h3 align="center">Bạn chưa có khiếu nại !</h3>
    @endif



@endsection



