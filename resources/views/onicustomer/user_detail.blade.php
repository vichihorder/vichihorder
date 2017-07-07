@extends($layout)

@section('page_title')
    {{@$page_title}}
@endsection

@section('content')
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Thông tin chung</h5>
                    </div>
                    <div>
                        <div class="ibox-content">
                            <fieldset class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Họ & tên: </label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static">{{$user->name}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Mã thành viên: </label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static">{{$user->code}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Cú pháp nạp tiền: </label>
                                    <div class="col-sm-9">
                                        <?php $user_mobile_default = $user->getMobile();?>
                                        <p class="form-control-static">
                                            NM {{$user->code}}
                                            @if($user_mobile_default)
                                                {{ $user_mobile_default  }}
                                            @else
                                                &lt;Số Điện Thoại&gt;
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Số dư: </label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static">{{ App\Util::formatNumber($user->account_balance)  }} <sup>đ</sup></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Email: </label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static">{{$user->email}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Trạng thái: </label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static">{{ App\User::getStatusName($user->status) }}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Gia nhập: </label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static">{{ App\Util::formatDate($user->created_at)  }}</p>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Điện thoại: </label>
                                    <div class="col-sm-9">
                                        @if(!empty($user_mobiles))
                                            <div id="_list-user-phone">
                                                @foreach($user_mobiles as $user_mobile)
                                                    <div class="input-group _row-user-phone">
                                                        <input type="text" disabled class="form-control" value="{{$user_mobile->mobile}}">
                                                        @if($permission['can_remove_mobile'])
                                                        <span class="input-group-btn">
                                                            <a data-phone="{{$user_mobile->mobile}}"
                                                               data-id="{{ $user_mobile->id }}" href="javascript:void(0)" class="btn btn-primary _remove-user-phone">
                                                                Xóa
                                                            </a>
                                                        </span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($permission['can_add_mobile'])
                                            <div class="input-group">
                                                <input type="text" class="form-control _input-user-phone" placeholder="Nhập điện thoại...">
                                                @if($permission['can_remove_mobile'])
                                                    <span class="input-group-btn">
                                                            <a href="javascript:void(0)" class="btn btn-primary _add-user-phone">
                                                                Thêm
                                                            </a>
                                                        </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Giới thiệu khách hàng</h5>
                    </div>
                    <div>
                        <div class="ibox-content">
                            <div class="input-group">
                                <input type="text" id="_link-user-register" class="form-control" value="{{ $user_refer['link']  }}">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-primary" data-clipboard-action="copy" data-clipboard-text="Đã sao chép."> Sao chép liên kết </button>
                                </span>
                            </div>

                            @if($user_refer['total'] > 0)
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>TT</th>
                                    <th>Khách hàng</th>
                                    <th>Số đơn đạt yêu cầu</th>
                                    <th>Hoa hồng tháng {{ date('m/Y')  }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($user_refer['data'] as $key => $val)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>

                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @else

                                <p>Hiện ban chưa giới thiệu khách nào!</p>
                            @endif
                        </div>
                    </div>
                </div>
@endsection

@section('footer-scripts')
    @parent

    <!-- 2. Include library -->
    <script src="{{ asset('js/clipboard.min.js')  }}"></script>

    <!-- 3. Instantiate clipboard -->
    <script>
        var clipboard = new Clipboard('.btn', {
            target: function() {
                return document.getElementById('_link-user-register');
            }
        });

        clipboard.on('success', function(e) {
            console.log(e);
        });

        clipboard.on('error', function(e) {
            console.log(e);
        });
    </script>

    <script>
        $(document).ready(function(){

            $(document).on('click', '._remove-user-phone', function(){
                var user_phone = $(this).data('phone');
                var user_phone_id = $(this).data('id');

                var $that = $(this);

                $.ajax({
                    url: "{{ url('user/phone') }}",
                    method: 'put',
                    data: {
                        user_phone:user_phone,
                        user_phone_id:user_phone_id,
                        user_id: "{{$user_id}}",
                        _token: "{{csrf_token()}}"
                    },
                    success:function(response) {
                        if(response.success){
                            $that.parents('._row-user-phone').remove();
                        }else{
                            bootbox.alert(response.message);
                        }
                    },
                    error: function(){


                    }
                });
            });

            $(document).on('click', '._add-user-phone', function(){
                var user_phone = $('._input-user-phone').val();

                $.ajax({
                    url: "{{ url('user/phone')  }}",
                    method: 'post',
                    data: {
                        user_phone:user_phone,
                        user_id: "{{$user_id}}",
                        _token: "{{csrf_token()}}"
                    },
                    success:function(response) {
                        if(response.success){
                            window.location.reload();
                        }else{
                            $('._input-user-phone').focus();
                            bootbox.alert(response.message);
                        }
                    },
                    error: function(){

                    }
                });
            });
        });
    </script>
@endsection