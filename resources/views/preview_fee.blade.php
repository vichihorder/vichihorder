@extends('layouts/layout_preview_fee')

@section('page_title')
    {{$page_title}}
@endsection

@section('content')

    <a href="{{ url('')  }}"><< Về trang chủ</a>

                <h1>Công cụ tính phí</h1>
                <p>Vui lòng điền đẩy đủ thông tin bên duới để chúng tôi tính phí giúp bạn:
                    <small>
                        <br> - Tổng tiền hàng. VD: đơn hàng của bạn đặt 5 sản phẩm, mỗi sản phẩm có giá là 5tệ, tiền hàng lúc đó là 5 x 5tệ
                        <br> - Phí vận chuyển nội địa TQ (là phí vận chuyển từ shop người bán TQ tới kho của NhatMinh247 bên TQ, tùy từng shop mà phí có thể khác nhau, có thể mất phí hoặc miễn phí)
                        <br> - Cân nặng tính phí (nếu hàng cồng kềnh, chúng tôi sẽ quy ra cân nặng quy đổi, khi đó cân nặng nào lớn hơn sẽ được dùng để tính phí)
                        VD: cân nặng tịnh là 10kg, cân nặng quy đổi là: 2kg, cân nặng tính phí là 10kg và trường hợp ngược lại


                    </small>
                </p>
                <p>Tỉ giá hiện tại: {{App\Util::formatNumber($exchange_rate)}}đ</p>

                <form class="form-horizontal" action="{{ url('tinh-phi')  }}" method="get">
                    <input type="hidden" name="is_submit" value="1">

                    <fieldset>
                        <legend>Thông tin địa chỉ:</legend>
                        <div class="row">
                            <label for="">Tỉnh/thành phố:</label>
                            <select style="width: 200px;" required id="province_id" autofocus name="province_id" class="_autofocus form-control">
                                <option value="">Mời chọn</option>
                                @foreach($all_provinces as $province)
                                    <option
                                            @if(isset($data['province_id'])
                                            && $province->id == $data['province_id']) selected @endif

                                    value="{{$province->id}}">{{$province->label}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <label for="">Quận/huyện:</label>
                            <select style="width: 200px;" required id="district_id" name="district_id" class="form-control">
                                <option value="">Mời chọn</option>
                                @foreach($all_districts as $district)
                                    <option
                                            @if(isset($data['district_id'])
                                            && $district->id == $data['district_id']) selected @endif
                                    class="hidden"
                                            data-province-id="{{ $district->parent_id  }}"
                                            value="{{$district->id}}">
                                        {{$district->label}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </fieldset>

                    <fieldset>
                        <legend>Phí mua hàng</legend>
                        <div class="row">
                            <label for="">Tiền hàng:</label>
                            <input required type="text" name="amount" value="{{@$data['amount']}}" class="form-control">
                            đơn vị
                            <select name="type_amount" id="">
                                @foreach($all_type_amount as $key => $val)
                                    <option
                                            @if(isset($data['type_amount']) && $data['type_amount'] == $key)
                                            selected
                                            @endif

                                            value="{{$key}}">{{$val}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <label for="">Phí mua hàng:</label>
                            @if($return_data)
                                {{ App\Util::formatNumber($return_data['buying_fee'])  }}
                            @else
                                0
                            @endif
                            đ
                        </div>


                    </fieldset>

                    <fieldset>
                        <legend>Phí vận chuyển</legend>
                        <div class="row">
                            <label for="">VC nội địa TQ: </label>
                            <input
                                    value="{{ @$data['domestic_shipping_china']  }}"
                                    type="text" name="domestic_shipping_china" class="form-control">
                            đơn vị
                            <select name="type_domestic_shipping_china" id="">
                                @foreach($all_type_domestic_shipping_china as $key => $val)
                                    <option
                                            @if(isset($data['type_domestic_shipping_china']) && $data['type_domestic_shipping_china'] == $key)
                                            selected
                                            @endif

                                            value="{{$key}}">{{$val}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <label for="">Cân nặng tính phí (kg):</label>
                            Tịnh <input
                                    @if((isset($data['type_weight']) && $data['type_weight'] == 1)
                                    || !isset($data['type_weight']))
                                    checked
                                    @endif
                                    type="radio" name="type_weight" value="1">
                            <input type="text" name="weight" value="{{@$data['weight']}}">

                            Quy đổi <input
                                    @if( isset($data['type_weight']) && $data['type_weight'] == 2 )
                                    checked
                                    @endif
                                    type="radio" name="type_weight" value="2">

                            @if($return_data)
                                <input type="text" disabled value="{{$return_data['converted_weight']}}">
                            @else
                                <input type="text" disabled value="">
                            @endif
                        </div>

                        <div class="row type_weight" data-type="1" style="display: none;">

                        </div>

                        <div class="row type_weight" data-type="2" style="display: none;">
                            <label for="">Thể tích(cm):</label>
                            Dài <input type="text" name="length_package" value="{{@$data['length_package']}}">
                            x Rộng <input type="text" name="width_package" value="{{@$data['width_package']}}">
                            x Cao <input type="text" name="height_package" value="{{@$data['height_package']}}">
                        </div>



                        <div class="row">
                            <label for="">VC quốc tế TQ - VN:</label>
                            @if($return_data)
                                {{ App\Util::formatNumber($return_data['shipping_china_vietnam'])  }}
                            @else
                                0
                            @endif
                            đ
                        </div>
                    </fieldset>

                    <h3>Toàn bộ số tiền bạn cần thanh toán cho NhatMinh247 là:


                        @if($return_data)
                            {{ App\Util::formatNumber($return_data['total_fee'])  }}
                        @else
                            0
                        @endif
                        đ

                    </h3>

                    <button type="submit" class="btn btn-danger">Tính</button>

                </form>


@endsection

@section('css_bottom')
    @parent
    <link rel="stylesheet" href="{{asset('css/preview-fee.css')}}">
@endsection

@section('js_bottom')
    @parent
    <script>
        $(document).ready(function(){

            var pro_id = "{{ @$data['province_id']  }}";
            if(pro_id){
                showDistrictByProvince(pro_id);
            }

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

            chooseTypeWeight("{{ @$data['type_weight']  }}");

            $(document).on('change', 'input[name="type_weight"]', function(){
                var type = $(this).val();
                chooseTypeWeight(type);
            });

            function chooseTypeWeight(type){
                $('.type_weight').hide();
                if(type){
                    $('.type_weight[data-type="'+type+'"]').show();
                }
            }
        });

    </script>
@endsection

