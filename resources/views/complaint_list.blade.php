@extends('layouts.app')

@section('page_title')
    {{$page_title}}
@endsection

@section('content')

    <h4>Danh sách khiếu nại</h4>
    <br>
    <form method="get" action="{{ url('complaint')}}" >
    <div class="form-group">
        <div class="row">

            <div class="col-sm-3">
                <input type="text"
                       class="form-control"
                       value="{{ @request()->get('username')  }}"
                       name="username"
                       placeholder="Nhập mã khách ">
            </div>
            <div class="col-sm-3">
                <input type="text" class="form-control" value="{{ @request()->get('ordercode')  }}" name="ordercode" placeholder="Nhập mã đơn hàng">
            </div>
            <div class="col-sm-3">
                <select class="selectpicker" name="status_complaint">
                    <option>Trạng thái của KN</option>
                    @foreach($complaint_status as $key => $val)
                        <option value="{{$key}}"
                            @if($key == @request()->get('status_complaint'))
                                selected
                             @endif
                        >
                            {{$val}}
                        </option>
                    @endforeach
                </select>

            </div>
            <div class="col-sm-3">
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            </div>

        </div>

    </div>
    </form>
    <table class="table table-bordered">
        <tr>
            <th>Mã đơn hàng</th>
            <th>Tên khiếu nại</th>
            <th>Trạng thái</th>
            <th>Khách hàng</th>
            <th>Thời gian tạo</th>
        </tr>
    @if(count($data) > 0)
        @foreach($data as $complaint_item)
        <tr>
            <td scope="row">{{ App\Complaints::getOrderCode($complaint_item->order_id) }}</td>
            <td>{{ $complaint_item->title }}</td>
            <td>{{ App\Complaints::$alias_array[$complaint_item->status] }}</td>
            <td>{{ App\Complaints::getCustomerUsername($complaint_item->customer_id) }}</td>
            <td>{{ $complaint_item->created_time }} <a href="{{ url('complaint-detail/'.$complaint_item->id) }}">   Xem chi tiết</a></td>
        </tr>
        @endforeach
    @endif
    </table>
    @if(!empty($data))
        {{ $data->links() }}
    @else
        <h3 align="center">Không có kết quả !</h3>
    @endif
@endsection
@section('css_bottom')
    @parent
    <link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
@endsection
@section('js_bottom')
    @parent
    <script type="text/javascript" src="{{ asset('js/bootstrap-select.min.js') }}"></script>
  <script>
      $(document).ready(function() {
          $('.selectpicker').selectpicker('refresh');
      });
  </script>
@endsection