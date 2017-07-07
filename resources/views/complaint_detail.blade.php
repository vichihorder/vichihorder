@extends('layouts.app')

@section('page_title')
    {{$page_title}}
@endsection

@section('content')

    <h4>Chi tiết khiếu nại</h4>
    <table class="table table-bordered">
        <tr>
            <th>Mã đơn hàng</th>
            <th>Tên khiếu nại</th>
            <th>Trạng thái</th>
            <th>Mô tả lỗi</th>
            <th>Ảnh</th>
        </tr>

        <tr>
            {{--<td scope="row">{{ \App\Complaints::getOrderCode($data_complaint->order_id) }}</td>--}}
            {{--<td>{{$data_complaint->title}}</td>--}}
            {{--<td>{{App\Complaints::$alias_array[$data_complaint->status]}}</td>--}}
            {{--<td>--}}
                {{--{{ $data_complaint->description }}--}}
            {{--</td>--}}
            {{--<td>--}}
                {{--@foreach($data_complaint_file as $complaint_item)--}}
                    {{--<img src="{{ asset($complaint_item->path) }}" width="90px" height="90px">--}}
                {{--@endforeach--}}
            {{--</td>--}}

        </tr>

    </table>



    <div class="col-sm-4 col-xs-12" id="anchor-box-comment">
        @include('partials/__comment', [
            'object_id' => $data_complaint->order_id,
            'object_type' => App\Comment::TYPE_OBJECT_COMPLAINT,
             #'scope_view' => App\Comment::TYPE_EXTERNAL

        ])

    </div>

@endsection

@section('js_bottom')
    @parent
    <script type="text/javascript" src="{{ asset('js/jquery.lazy.min.js') }}"></script>
@endsection