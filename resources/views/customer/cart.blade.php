@extends($layout)

@section('page_title')
    {{@$page_title}}
@endsection

@section('content')

    <div class="row">
        <div class="col-xs-12">
            @include('partials/__cart_step', ['active' => 1])
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="pull-left">
                <strong>{{$data['statistic']['total_shops']}}</strong> shop / <strong>{{$data['statistic']['total_items']}}</strong> sản phẩm / <strong><span class="">{{ App\Util::formatNumber($data['statistic']['total_amount'])  }}</span></strong>đ tiền hàng
            </div>

            {{--<a href="{{ url('dat-coc?shop_id=' . implode(',', $data['shop_ids']))  }}" class="btn btn-danger pull-right text-uppercase">--}}
                {{--Đặt cọc {{$data['statistic']['total_shops']}} shop--}}
            {{--</a>--}}
        </div>
    </div>

    <br>

    @if(!empty($data['shops']))
        @foreach($data['shops'] as $shop)

        <div class="row _shop" data-shop-id="{{$shop->shop_id}}">
            <div class="col-xs-12">
                <div class="card ">
                    <div class="card-header" style="position: relative">

                        <form class="___form">
                            <input type="hidden" name="action" value="remove_shop">
                            <input type="hidden" name="method" value="post">
                            <input type="hidden" name="shop_id" value="{{$shop->shop_id}}">
                            <input type="hidden" name="url" value="{{ url('gio-hang/hanh-dong') }}">
                            <input type="hidden" name="confirm" value="Bạn có chắc muốn xoá shop này?">
                            <input type="hidden" name="response" value="customer/cart">
                            <input type="hidden" name="_token" value="{{ csrf_token()  }}">

                            <a href="javascript:void(0)"
                               class="___btn-action"
                               data-toggle="tooltip"
                               title="Xoá shop">
                                {{--<i class="fa fa-times"></i>--}}
                                <i class="fa fa-trash-o"></i>
                            </a>
                        </form>

                        &nbsp;&nbsp;&nbsp;

                        {!! App\Util::showSite($shop->site) !!}

                        @if($shop->shop_name)
                            {{$shop->shop_name}}
                        @elseif($shop->shop_id)
                            {{$shop->shop_id}}
                        @endif

                        <div style="position: absolute;top: 18px;right: 20px;">
                            @foreach($data['services'] as $service)
                                <div class="checkbox-inline">

                                    <form class="___form">
                                        <input type="hidden" name="action" value="choose_service">
                                        <input type="hidden" name="method" value="post">
                                        <input type="hidden" name="shop_id" value="{{$shop->shop_id}}">
                                        <input type="hidden" name="url" value="{{ url('gio-hang/hanh-dong') }}">
                                        <input type="hidden" name="response" value="customer/cart">
                                        <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                        <input type="hidden" name="service" value="{{$service['code']}}">

                                        <input
                                                @if(in_array($service['code'], $shop->services)) checked @endif
                                                type="checkbox"
                                                value="{{$service['code']}}"
                                                class="___btn-action"
                                                id="checkbox_{{$service['code']}}_{{$shop->id}}">

                                    </form>


                                    <label for="checkbox_{{$service['code']}}_{{$shop->id}}">
                                        {{$service['title']}}
                                    </label>
                                </div>
                            @endforeach
                        </div>


                    </div>
                    <div class="card-body no-padding">
                        <div class="table-responsive">
                            <table class="table card-table">
                                <thead>
                                <tr>
                                    <th width="5%"></th>
                                    <th width="50%">Sản phẩm </th>
                                    <th width="15%">Đơn giá </th>
                                    <th width="15%" class="">SL</th>
                                    <th width="15%">Tiền hàng </th>

                                </tr>
                                </thead>
                                <tbody>

                                @foreach($shop->items as $item)
                                <tr class="_shop-item" data-shop-id="{{$shop->shop_id}}" data-shop-item-id="{{$item->id}}">
                                    <td class="text-center">

                                        <form class="___form">
                                            <input type="hidden" name="action" value="remove_item">
                                            <input type="hidden" name="method" value="post">
                                            <input type="hidden" name="shop_id" value="{{$shop->shop_id}}">
                                            <input type="hidden" name="item_id" value="{{$item->id}}">
                                            <input type="hidden" name="url" value="{{ url('gio-hang/hanh-dong') }}">
                                            <input type="hidden" name="response" value="customer/cart">
                                            <input type="hidden" name="_token" value="{{ csrf_token()  }}">

                                            <a class="___btn-action"
                                               href="javascript:void(0)"
                                               data-toggle="tooltip"
                                               title="Xoá sản phẩm">
                                                <i class="fa fa-trash-o"></i>
                                            </a>
                                        </form>

                                    </td>

                                    <td>
                                        <a href="{{ str_replace('150x150', '600x600', urldecode($item->image_model))  }}" data-lightbox="image-1">
                                            <img style="margin-right: 10px;"
                                                 src="{{ urldecode($item->image_model) }}"
                                                 class="pull-left"
                                                 width="100px" />
                                        </a>

                                        <a href="{{$item->link_origin}}" target="_blank">
                                            {{$item->title_origin}}
                                        </a>
                                        <br />
                                        <small>{{$item->property}}</small>
                                        <br>



                                        <input
                                                data-toggle="_tooltip"
                                                data-shop-id="{{$shop->shop_id}}"
                                                data-item-id="{{$item->id}}"
                                                placeholder="Ghi chú sản phẩm..."
                                                style="width: 250px; padding: 0 5px;"
                                                name="comment"
                                                type="text"
                                                data-url="{{ url('gio-hang/hanh-dong') }}"
                                                data-method="post"
                                                data-key-global="cart-item-comment-{{$shop->shop_id}}-{{$item->id}}"
                                                class="__input-comment-item" value="{{$item->comment}}" />
                                    </td>

                                    <td>
                                        <span class="text-danger">{{ App\Util::formatNumber($item->price_calculator_vnd)  }}đ</span> / <span class="text-success">¥{{$item->price_calculator}}</span>
                                    </td>
                                    <td>

                                        <form class="___form" onsubmit="return false;">
                                            <input type="hidden" name="action" value="update_quantity">
                                            <input type="hidden" name="method" value="post">
                                            <input type="hidden" name="shop_id" value="{{$shop->shop_id}}">
                                            <input type="hidden" name="item_id" value="{{$item->id}}">
                                            <input type="hidden" name="url" value="{{ url('gio-hang/hanh-dong') }}">
                                            <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                            <input type="hidden" name="response" value="customer/cart">

                                            <input
                                                    data-toggle="_tooltip"
                                                    style="width: 80px"
                                                    type="number"
                                                    data-key-global="cart-item-quantity-{{$shop->shop_id}}-{{$item->id}}"
                                                    name="quantity"
                                                    class="form-control text-center ___input-action" value="{{$item->quantity}}" />
                                        </form>

                                    </td>
                                    <td>
                                        <span class="text-danger">{{ App\Util::formatNumber($item->total_amount_item_vnd)  }}đ</span> / <span class="text-success">¥{{$item->total_amount_item}}</span>
                                    </td>



                                </tr>

                                @endforeach
                                <tr>
                                    <td class="text-right" colspan="5">

                                        Tiền hàng: <span class="text-danger">{{ App\Util::formatNumber($shop->total_amount_items)  }}đ</span> ;
                                        Mua hàng <span class="text-danger">{{ App\Util::formatNumber($shop->buying_fee)  }}đ</span>
                                        ; VC TQ - VN <span class="text-danger">0 đ</span>
                                        <i data-toggle="tooltip"
                                           title="Khi NhatMinh247 nhận hàng của bạn, lúc đó hệ thống sẽ tính toán phí này. Hiện giờ chưa rõ cân nặng để tính phí" class="fa fa-question-circle"></i>

                                        @if($shop->checkExistsCartService(App\Service::TYPE_WOOD_CRATING))
                                        ; Đóng gỗ <span class="text-danger">{{ App\Util::formatNumber($shop->wood_crating)  }}đ</span>
                                        <i data-toggle="tooltip"
                                           title="Khi NhatMinh247 nhận hàng của bạn, lúc đó hệ thống sẽ tính toán phí này. Hiện giờ chưa rõ cân nặng để tính phí" class="fa fa-question-circle"></i>

                                        @endif

                                        <a href="{{ url('dat-coc?shop_id=' . $shop->shop_id)  }}" class="btn btn-danger text-uppercase">Đặt cọc</a>

                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @endforeach
    @else
        <div class="row">
            <div class="col-sm-12">
                <div class="card ">
                    <div class="card-body text-center">
                        <h3>Giỏ hàng hiện đang trống!</h3>

                        {{--Click vào <a href="">đây</a> để được huớng dẫn đặt hàng một cách chi tiết nhất!--}}
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('css_bottom')
    @parent

    <link rel="stylesheet" href="{{ asset('bower_components/lightbox2/dist/css/lightbox.css')  }}">
@endsection

@section('js_bottom')
    @parent
    <script type="text/javascript" src="{{ asset('js/jquery.lazy.min.js') }}"></script>
    <script src="{{ asset('bower_components/lightbox2/dist/js/lightbox.js')  }}"></script>

    <script>
        $(function() {
            $('.lazy').lazy();

            $(document).on('change', '.__input-comment-item', function(){
                var shop_id = $(this).data('shop-id');
                var item_id = $(this).data('item-id');
                var comment = $(this).val();

//                var $item = $('._shop-item[data-shop-id="'+shop_id+'"][data-shop-item-id="'+item_id+'"]');
                request($(this).data('url'), $(this).data('method'), {
                    _token:"{{csrf_token()}}",
                    shop_id:shop_id,
                    item_id:item_id,
                    comment:comment,
                    action:'comment'
                }).done(function(response){
                    if(!response.success){
                        bootbox.alert(response.message);
                    }
                })
            });
        });
    </script>
@endsection

