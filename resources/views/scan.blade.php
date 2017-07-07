@extends($layout)

@section('page_title')
    {{@$page_title}}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                @include('partials/__breadcrumb',
                    [
                        'urls' => [
                            ['name' => 'Trang chủ', 'link' => url('home')],
                            ['name' => 'Quét mã vạch', 'link' => null],
                        ]
                    ]
                )
                <div class="card-body">

                    <div class="row">
                        <div class="col-sm-4 col-xs-12">
                            <h3>Quét mã vạch</h3>


                            <form onsubmit="return false;" class="___form" id="_from-scan-barcode">

                                <select name="action" id="" class="form-control">
                                    @if(!empty($action_list))
                                        @foreach($action_list as $key => $val)
                                            <option value="{{$key}}">
                                                {{$val}}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <br>

                                <select name="warehouse" id="" class="form-control">
                                    @if(!empty($warehouse_list))
                                        @foreach($warehouse_list as $key => $val)
                                            <option
                                                    data-warehouse-type="{{$val['type']}}"
                                                    value="{{$val['code']}}">{{$val['name']}} - {{$val['description']}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <br>

                                <input type="hidden" name="method" value="post">
                                <input type="hidden" name="url" value="{{ url('scan/action') }}">
                                <input type="hidden" name="_token" value="{{ csrf_token()  }}">

                                <input
                                        id="_scan-logistic-package-barcode"
                                        autofocus
                                        type="text"
                                        name="barcode"
                                        class="form-control _scan-barcode"
                                        data-key-global="barcode-scan-input"
                                        placeholder="Quét mã kiện">

                            </form>


                            {{--<hr>--}}
                            {{--<h4>Thống kê trong ngày</h4>--}}
                            {{--<br>--}}

                            {{--<div id="statistic"></div>--}}
                        </div>
                        <div class="col-sm-8 col-xs-12">
                            <ul class="list-group scroll" style="margin-top: 57px;">
                                {{--<li class="list-group-item">--}}
                                    {{--Mã kiện 8080080 Khách Nhung , số điện thoại 0909090 , địa chỉ :  số 8 ngách 3 ngõ 198 Lê Trọng Tấn – Định Công – Hà Nội--}}
                                    {{--<span style="color:green"><i class="fa fa-print" aria-hidden="true"></i></span>--}}
                                {{--</li>--}}
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
@section('cs_bottom')
    <style>
        .scroll{
            max-height: 300px;
            overflow-y:scroll;
        }
    </style>
@endsection

@section('js_bottom')
    @parent
    <script src="{{asset('js/notify.min.js')}}"></script>
    <script src="{{asset('js/ion.sound.min.js')}}"></script>

    <script>
        statistic();
        function statistic(){
            request('{{ url('scan/statistic')  }}', 'get', {}).done(function(response){
                if(response.success){
                    $('#statistic').html(response.html);
                }else{
                    bootbox.alert(response.message);
                }
            });
        }

        $(document).ready(function(){
            var arr_message = JSON.parse(localStorage.getItem('scan_action.')) ? JSON.parse(localStorage.getItem('scan_action.')) : [];
            LocalStorage.init();
            // init bunch of sounds
            ion.sound({
                sounds: [
                    {name: "success"},
                    {name: "error"}
                ],

                // main config
                path: "{{ asset('sounds')  }}/",
                preload: true,
                multiplay: true,
                volume: 0.9
            });


            $(document).on('keypress', '._scan-barcode', function(e){
                var that = this;
                if(e.keyCode == 13){
                    var barcode = $(this).val();
                    if(!barcode) return false;

                    request("{{ url('scan/action') }}", "post", $('#_from-scan-barcode').serializeObject()).done(function(response){
                        var msg_type = 'success';
                        if(response.success){
                            ion.sound.play("success");
                            if(response.result.order_id){
                                var packageBarcode = response.result.barcode;
                                var url_barcode = "package/"+packageBarcode;
//                                var message = " Kiện " + "<strong class='_click_barcode'>"+"<a href='"+ url_barcode +"' target='_blank'>" + response.result.barcode + "</a></strong>" + " khách hàng " +
//                                        response.result.address.reciver_name + " , địa chỉ " + response.result.address.detail + "<br/>" + response.result.message ;

                                var message = " Kiện " + "<strong class='_click_barcode'>"+"<a href='"+ url_barcode +"' target='_blank'>" + response.result.barcode + "</a></strong>" + response.result.message + "<br/>"
                                        + "Địa chỉ: " + " khách hàng " + response.result.address.reciver_name + ','  + response.result.address.detail;

                                arr_message.push(message);
                                LocalStorage.set('scan_action.', JSON.stringify(arr_message));

                                $(".list-group").prepend("<li class='list-group-item'>" + message + "</li>");

                            }else{
                                //$(".list-group").prepend("<li class='list-group-item'>Kiện chưa khớp đơn !</li>");
                            }
                        }else{
                            msg_type = 'error';
                            ion.sound.play("error");
                        }

                        if(response.message){
                            $.notify(response.message, msg_type);
                        }
                        $(that).val('').focus();

                        statistic();
                    });
                }
            });
        });

// giá trị localstorage bên trung quốc

var LocalStorage = {
            init : function () {
               var package_item = JSON.parse(localStorage.getItem('scan_action.')) ? JSON.parse(localStorage.getItem('scan_action.')) : []
                for (var i = 0; i < package_item.length; i++) {
                        $(".list-group").prepend("<li class='list-group-item'>" + package_item[i] + "</li>");
                     }
            },
            prefix: '',
            setPrefix: function(prefix) {
                this.prefix = prefix;
            },
            get: function (key, defaultVal) {
                var val = localStorage.getItem(this.prefix + key);
                if (!val && defaultVal) {
                    return defaultVal;
                }
                return val;
            },
            set: function (key, val) {
                localStorage.setItem(this.prefix + key, val);
            }
};
    </script>
@endsection

