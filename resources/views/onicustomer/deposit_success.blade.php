@extends('onilayouts.app')

@section('page_title')
    {{$page_title}}
@endsection

@section('content')
    <div class="wrapper wrapper-content">
        @include('onipartials/__cart_step', ['status' => array(2,1,0,0)])
        <div class="row">
            <div class="col-md-6 col-md-offset-3 animated fadeInUp">
                <div class="widget-head-color-box navy-bg p-lg text-center">
                    <div class="m-b-md">
                        <img src="data:image/svg+xml;base64,CjxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDEwMDAgMTAwMCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMTAwMCAxMDAwIiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPG1ldGFkYXRhPiBTdmcgVmVjdG9yIEljb25zIDogaHR0cDovL3d3dy5vbmxpbmV3ZWJmb250cy5jb20vaWNvbiA8L21ldGFkYXRhPgogIDxnPjxwYXRoIGQ9Ik01MDAsMTBDMjI4LjksMTAsMTAsMjI4LjksMTAsNTAwYzAsMjcxLjEsMjE4LjksNDkwLDQ5MCw0OTBjMjcxLjEsMCw0OTAtMjE4LjksNDkwLTQ5MEM5OTAsMjI4LjksNzcxLjEsMTAsNTAwLDEweiBNNDk2LjcsOTMxLjJDMjU4LjMsOTMxLjIsNjUuNSw3MzguNSw2NS41LDUwMFMyNTguMyw2OC44LDQ5Ni43LDY4LjhTOTI3LjksMjYxLjUsOTI3LjksNTAwUzczNS4yLDkzMS4yLDQ5Ni43LDkzMS4yeiIgc3R5bGU9ImZpbGw6I0ZGRkZGRiI+PC9wYXRoPjxwYXRoIGQ9Ik0yMTIuNSw0MzEuNGMxNi4zLDAsMjkuNCwxMy4xLDI5LjQsMjkuNGMwLDE2LjMtMTMuMSwyOS40LTI5LjQsMjkuNGMtMTYuMywwLTI5LjQtMTMuMS0yOS40LTI5LjRDMTgzLjEsNDQ0LjUsMTk2LjIsNDMxLjQsMjEyLjUsNDMxLjR6IiBzdHlsZT0iZmlsbDojRkZGRkZGIj48L3BhdGg+PHBhdGggZD0iTTc2MS4zLDMwNGMxNi4zLDAsMjkuNCwxMy4xLDI5LjQsMjYuMWMwLDE2LjMtMTMuMSwyNi4xLTI5LjQsMjYuMXMtMjkuNC0xMy4xLTI5LjQtMjYuMUM3MzEuOSwzMTcuMSw3NDUsMzA0LDc2MS4zLDMwNHoiIHN0eWxlPSJmaWxsOiNGRkZGRkYiPjwvcGF0aD48cGF0aCBkPSJNNDI0LjksNjQwLjVjMTYuMywwLDI5LjQsMTMuMSwyOS40LDI5LjRzLTEzLjEsMjkuNC0yOS40LDI5LjRjLTE2LjMsMC0yOS40LTEzLjEtMjkuNC0yOS40UzQwOC41LDY0MC41LDQyNC45LDY0MC41eiIgc3R5bGU9ImZpbGw6I0ZGRkZGRiI+PC9wYXRoPjxwYXRoIGQ9Ik0yMzUuNCw0MzcuOWwyMTIuMywyMTIuM2wtNDIuNSw0Mi41TDE5Mi45LDQ4MC40TDIzNS40LDQzNy45eiIgc3R5bGU9ImZpbGw6I0ZGRkZGRiI+PC9wYXRoPjxwYXRoIGQ9Ik03NDEuNywzMTAuNUw0MDguNSw2NDMuN2w0Mi41LDQyLjVMNzgwLjksMzUzTDc0MS43LDMxMC41eiIgc3R5bGU9ImZpbGw6I0ZGRkZGRiI+PC9wYXRoPjwvZz48L3N2Zz4KICA=" width="128" height="128">
                    </div>
                    <div class="">
                        <h1 class="font-bold no-margins">
                            Đặt cọc thành công
                        </h1>
                    </div>
                </div>
                @if(count($orders))
                    @foreach($orders as $order)
                        <div class="widget-text-box">
                            <img style="float: left; margin-right: 10px;width: 48px;height: 48px;" src="{{ $order->avatar }}" width="50px" alt="">
                            <h4 class="media-heading"><a href="{{ url('don-hang', $order->id)  }}" title="Xem lại đơn #{{$order->code}}">#{{$order->code}}</a></h4>
                            <p>Đã đặt cọc.</p>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection

@section('js_bottom')
    @parent

    <script type="text/javascript" src="{{ asset('js/jquery.lazy.min.js') }}"></script>
    <script>
        $(function() {
            $('.lazy').lazy();
        });
    </script>

    <script>
        $(document).ready(function(){


        });

    </script>
@endsection

