@extends('layouts.app')

@section('page_title')
    {{$page_title}}
@endsection

@section('content')
    <table class="table table-hover table-bordered">
        <thead>
        <tr>
            <th>STT</th>
            <th>Thông báo</th>
            <th>Nội dung</th>
            <th>Thời gian</th>
        </tr>
        </thead>
        <tbody>
        @if(!empty($data))
            <?php $i = 1; ?>
            @foreach($data as $notification)
            <tr>
                <th scope="row">{{ $i++ }}</th>
                @if($notification->type == 'ORDER')
                    <td>Thông báo trên đơn</td>
                @else
                    <td>Thông báo tài chính</td>
                @endif
                <td>{{ $notification->notification_content }}</td>
                <td>{{ $notification->created_time }}</td>
            </tr>
            @endforeach
        @endif
        </tbody>
    </table>
    @if(!empty($data))
    {{ $data->links() }}
    @else
    <h3 align="center">Chưa có thông báo mới !</h3>
    @endif



@endsection



