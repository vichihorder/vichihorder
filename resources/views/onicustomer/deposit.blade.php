@extends('onilayouts.app')

@section('page_title')
    {{@$page_title}}
@endsection

@section('content')
    <div class="wrapper wrapper-content">
        @include('onipartials/__cart_step', ['status' => array(2,1,0,0)])
        <div class="row">
            @if(count($shops))
                <div class="col-md-6">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>
                                Địa chỉ nhận hàng
                            </h5>
                        </div>
                        <div class="ibox-content">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Thông tin địa chỉ</th>
                                    <th width="100">Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($user_address as $idx => $user_address_item)
                                    <?php
                                    $province = App\Location::find($user_address_item->province_id)->label;
                                    $district = App\Location::find($user_address_item->district_id)->label;
                                    ?>
                                    <tr class="_user-address"
                                        data-is-default="{{ $user_address_item->is_default }}"
                                        data-id="{{$user_address_item->id}}">
                                        <td>
                                            <p>
                                                <strong>
                                                    <i class="fa fa-user-circle-o" aria-hidden="true"></i> {{$user_address_item->reciver_name}} / <i class="fa fa-phone" aria-hidden="true"></i> {{$user_address_item->reciver_phone}}
                                                </strong>

                                                @if($user_address_item->is_default)
                                                    <span class="label label-danger text-uppercase">Mặc định</span>
                                                @endif
                                            </p>

                                            <p><i class="fa fa-map-marker" aria-hidden="true"></i> {{$user_address_item->detail}}, {{$district}}, {{$province}}</p>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a data-toggle="tooltip" title="Sửa địa chỉ" href="javascript:void(0)" data-id="{{$user_address_item->id}}" data-json="{{ json_encode($user_address_item)  }}" class="btn-white btn btn-xs _btn-action-edit">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                &nbsp;&nbsp;
                                                <a data-toggle="tooltip" title="Xóa địa chỉ" href="javascript:void(0)" data-id="{{$user_address_item->id}}" class="btn-white btn btn-xs _btn-action-delete">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                                &nbsp;&nbsp;
                                                @if(!$user_address_item->is_default)
                                                    <a data-toggle="tooltip" title="Đặt mặc định" href="javascript:void(0)" data-id="{{$user_address_item->id}}" class="btn-white btn btn-xs _btn-action-set-default">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            @if($max_user_address)
                                <a class="btn btn-primary" id="_add-user-address">Thêm địa chỉ</a>
                            @endif

                            <div class="modal inmodal fade" id="modal-id" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">Cập nhật địa chỉ nhận hàng</h4>
                                        </div>
                                        <div class="modal-body">
                                            <form id="_form-update-user-address"  class="form-horizontal" action="{{ url('user/address')  }}" method="post">

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label" for="reciver_name">Tên người nhận</label>
                                                    <div class="col-sm-10">
                                                        <input required type="text" id="reciver_name" name="reciver_name" class="form-control" placeholder="Tên người nhận">
                                                    </div>
                                                </div>
                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label" for="reciver_phone">Điện thoại</label>
                                                    <div class="col-sm-10">
                                                        <input required type="text" id="reciver_phone" name="reciver_phone" class="form-control" placeholder="Điện thoại">
                                                        <span class="help-block m-b-none">Vui lòng chỉ nhập ký tự số.</span>
                                                    </div>
                                                </div>
                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label" for="province_id">Địa chỉ</label>
                                                    <div class="col-sm-10">
                                                        <div class="row m-b">
                                                            <div class="col-md-6">
                                                                <select required id="province_id" name="province_id" class="_autofocus form-control" id="">
                                                                    <option value="">Tỉnh / thành phố</option>
                                                                    @foreach($all_provinces as $province)
                                                                        <option value="{{$province->id}}">{{$province->label}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select required id="district_id" name="district_id" class="form-control" id="">
                                                                    <option value="">Quận / huyện</option>
                                                                    @foreach($all_districts as $district)
                                                                        <option class="hidden" data-province-id="{{ $district->parent_id  }}" value="{{$district->id}}">{{$district->label}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <input required type="text" id="detail" name="detail" class="form-control" placeholder="Địa chỉ">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label" for="note">Ghi chú</label>
                                                    <div class="col-sm-10">
                                                        <textarea id="note" name="note" rows="3" class="form-control" placeholder="Ghi chú"></textarea>
                                                    </div>
                                                </div>

                                                <input type="hidden" name="user_address_id" id="user_address_id" value="0">

                                            </form>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" id="_btn-update-user-address">Cập nhật</button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="ibox">
                        <div class="ibox-title">
                            <span class="pull-right">(<strong>{{count($shops)}}</strong>) shop</span>
                            <h5>
                                Danh sách shop kết đơn
                            </h5>
                        </div>
                        <div class="ibox-content">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped shoping-cart-table">
                                    <thead>
                                    <th colspan="2">Shop</th>
                                    <th class="text-center" width="100">SL / Link</th>
                                    <th class="text-right" width="100">Tiền hàng</th>
                                    </thead>

                                    @foreach($shops as $shop)
                                        <tr class="_shop-item" data-json="{{ json_encode($shop)  }}">
                                            <td width="90">
                                                <?php
                                                $avatar = urldecode($shop->avatar);
                                                ?>
                                                <div class="cart-product-imitation" style="padding-top: 0;">
                                                    <img src="{{ $avatar }}" style="width: 100%; height: 100%;">
                                                </div>
                                            </td>
                                            <td class="desc">
                                                <h3>
                                                    {!! App\Util::showSite($shop->site) !!}
                                                    <a target="_blank" class="text-navy">
                                                        @if($shop->shop_name)
                                                            {{$shop->shop_name}}
                                                        @elseif($shop->shop_id)
                                                            {{$shop->shop_id}}
                                                        @endif
                                                    </a>
                                                </h3>
                                            </td>
                                            <td style="text-align: center;">
                                                {{ $shop->total_quantity  }} / {{ $shop->total_link  }}
                                            </td>
                                            <td>
                                                <strong>
                                                    <span class="sub_total_vnd">{{ App\Util::formatNumber($shop->total_amount)  }}</span><sup>đ</sup>
                                                </strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                            <div class="table">
                                <table class="table invoice-total">
                                    <tbody>
                                    <tr>
                                        <td><strong>Tổng tiền hàng :</strong></td>
                                        <td style="min-width: 100px;">
                                            <span class="">{{ App\Util::formatNumber($total_amount_shop) }}</span><sup>đ</sup>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Đặt cọc ({{$deposit_percent}}%) :</strong></td>
                                        <td style="min-width: 100px;">
                                            <span class="">{{ App\Util::formatNumber($deposit_amount) }}</span><sup>đ</sup>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Số dư hiện tại :</strong></td>
                                        <td style="min-width: 100px;">
                                            <span class="">{{ App\Util::formatNumber(Auth::user()->account_balance) }}</span><sup>đ</sup>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            @if(Auth::user()->account_balance >= $deposit_amount)
                                <form action="" class="form-inline">
                                    <p>Nhập mật khẩu tài khoản để xác nhận đặt cọc.</p>
                                    <div class="input-group">
                                        <input placeholder="Nhập mật khẩu" type="password" class="form-control _input-password">
                                        <span class="input-group-btn">
                                                <button type="button" class="btn btn-primary _action-deposit">Đặt cọc</button>
                                            </span>
                                    </div>
                                </form>
                            @else
                                <div class="text-danger">
                                    Hiện số tiền trong tài khoản không đủ để đặt cọc. <br>
                                    Hiện bạn còn thiếu {{  App\Util::formatNumber(abs(Auth::user()->account_balance - $deposit_amount)) }} <sup>d</sup> <br>
                                    Vui lòng nạp tiền vào tài khoản để tiến hành đặt cọc đơn hàng. <br>
                                    Liên hệ HOTLINE 04.2262.6699 - 04.2265.6699 để được hỗ trợ!
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <h4>Không có sản phẩm ở SHOP này.</h4>
            @endif
        </div>
    </div>
@endsection

@section('header-scripts')
@endsection


@section('footer-scripts')
    @parent
    <script>
        $(document).ready(function(){


            $(document).on('keypress', '._input-password', function(e){
                 if(e.keyCode == 13){
                     e.preventDefault();
                     $('._action-deposit').click();
                 }
            });

            $(document).on('click', '._action-deposit', function () {
                 var $that = $(this);


                 var password = $('._input-password').val();
                 var address_id = $('._user-address[data-is-default=1]').data('id');

                 if(!$('._user-address').length){
                     swal({
                         title: "Lỗi!",
                         text: "Hiện chưa có địa chỉ nhận hàng!"
                     });
                     return false;
                 }

                 if(!address_id){
                     swal({
                         title: "Lỗi!",
                         text: "Vui lòng thiết lập 1 địa chỉ nhận hàng làm mặc định!"
                     });
                     return false;
                 }

                $that.prop('disabled', true);

                 $.ajax({
                   url: "{{ url('dat-coc')  }}",
                   method: 'post',
                   data: {
                       password: password,
                       shop_id: nhatminh247.shop_id,
                       address_id: address_id,
                       _token: "{{csrf_token()}}"
                   },
                   success:function(response) {
                       if(!response.success){
                           $that.prop('disabled', false);
                           swal({
                               title: "Lỗi!",
                               text: response.message
                           });
                           $('._input-password').focus();
                       }else{
                           window.location.href = response.redirect_url;
                       }
                   },
                   error: function(){
                       $that.prop('disabled', false);
                   }
                 });
            });

            $(document).on("change", "#province_id", function(event){
                var province_id = $(this).val();
                $('#district_id option:first').prop('selected', true);
                $('#district_id option:not(:first)').prop('selected', false);
                showDistrictByProvince(province_id);
            });

            function showDistrictByProvince(province_id){
                $('#district_id option:first').removeClass('hidden');
                $('#district_id option:not(:first)').addClass('hidden');

                if(province_id){
                    $('#district_id option[data-province-id=' + province_id + ']').removeClass('hidden');
                }
                $('#district_id').trigger('change');
            }

            $(document).on('click', '._btn-action-set-default', function () {
                var user_address_id = $(this).data('id');
                $.ajax({
                    url: "{{ url('user/address/default')  }}",
                    method: 'put',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: user_address_id
                    },
                    success: function(response){
                        if(response.success){
                            window.location.reload();
                        }
                    },
                    error: function () {

                    }
                });
            });

            $(document).on('click', '._btn-action-delete', function () {
                var user_address_id = $(this).data('id');
                swal({
                    title: "Bạn muốn xóa?",
                    text: "Sau khi xóa địa chỉ này, bạn không thể hoàn tác!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    cancelButtonText: "Hủy bỏ",
                    confirmButtonText: "Xóa địa chỉ",
                    closeOnConfirm: false
                }, function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: "{{ url('user/address/delete')  }}",
                            method: 'put',
                            data: {
                                _token: "{{ csrf_token() }}",
                                action: 'delete',
                                id: user_address_id
                            },
                            success: function(response){
                                if(response.success){
                                    window.location.reload();
                                }
                            },
                            error: function () {

                            }
                        });
                        swal("Đã xóa thành công!", "Bạn đã xóa địa chỉ thành công.", "success");
                    } else {
                        swal("Đã hủy", "Đã hủy bỏ thao tác xóa địa chỉ này.", "error");
                    }
                });
            });

            $(document).on('click', '._btn-action-edit', function(event){
                var id = $(this).data('id');
                var data_json = $(this).data('json');

                $('#_form-update-user-address').setFormData(data_json);

                showDistrictByProvince(data_json.province_id);

                $('#user_address_id').val(id);
                $('#modal-id').modal('show');
            });

            $(document).on('click', '#_add-user-address', function(event){

                $('#_form-update-user-address').setFormData({
                    province_id:'',
                    district_id:'',
                    note:'',
                    reciver_phone:'',
                    reciver_name:'',
                    detail:''
                });

                showDistrictByProvince(0);

                $('#user_address_id').val(0);
                $('#modal-id').modal('show');
            });

            $(document).on('click', '#_btn-update-user-address', function () {

                var data = $('#_form-update-user-address').serializeObject();
                    data._token = "{{ csrf_token() }}";

                $.ajax({
                    url: "{{ url('user/address')  }}",
                    method: 'post',
                    data:data,
                    success: function(response){
                        if(response.success){
                            window.location.reload();
                        }else{
                            swal({
                                title: "Lỗi!",
                                text: response.message,
                                html: true
                            });
                        }
                    },
                    error: function () {

                    }
                });
            });

        });

    </script>
@endsection

