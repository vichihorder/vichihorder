@extends($layout)

@section('page_title')
    {{@$page_title}}
@endsection

@section('content')
    <div class="wrapper wrapper-content">
        @include('onipartials/__cart_step', ['status' => array(1,0,0,0)])
        <div class="row">
            <div class="col-md-9">
                @if(!empty($data['shops']))
                    @foreach($data['shops'] as $shop)


                        <div id="shop-{{$shop->shop_id}}" class="shop site_{{$shop->site}} ibox">
                            <div class="ibox-title">
                                <span class="pull-right">(<strong class="shop_items_count">{{count($shop->items)}}</strong>) sản phẩm</span>
                                <h5>
                                    <img src="{{ asset('images/site/'.$shop->site.'.png') }}" class="site-logo" title="Được đặt trên website {{$shop->site}}">

                                    @if($shop->shop_name)
                                        {{$shop->shop_name}}
                                    @elseif($shop->shop_id)
                                        {{$shop->shop_id}}
                                    @endif
                                </h5>
                            </div>
                            <div class="ibox-content">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped shoping-cart-table">
                                        <thead>
                                        <th colspan="2">Sản phẩm</th>
                                        <th class="text-right">Đơn giá</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-right">Tiền hàng</th>
                                        </thead>

                                        @foreach($shop->items as $item)
                                            <tr id="shop-item-{{$item->id}}" class="shop-item" data-shop-id="{{$shop->shop_id}}">
                                                <td width="90">
                                                    <div class="cart-product-imitation" style="padding-top: 0;">
                                                        <img src="{{ urldecode($item->image_model) }}" style="width: 100%; height: 100%;">
                                                    </div>
                                                </td>
                                                <td class="desc">
                                                    <h3>
                                                        <a href="{{$item->link_origin}}" target="_blank" title="Đễn trang sản phẩm">
                                                            <i class="fa fa-external-link" aria-hidden="true"></i>
                                                        </a>
                                                        <a href="{{$item->link_origin}}" target="_blank" class="text-navy">
                                                            {{$item->title_origin}}
                                                        </a>
                                                    </h3>
                                                    <!-- Mo ta san pham --
                                                    <p class="small">
                                                        Mô tả
                                                    </p>
                                                    <!--/ Mo ta san pham -->
                                                    <dl class="small m-b-none">
                                                        <dt style="display: inline-block; margin-right: 10px;">Thuộc tính:</dt>
                                                        <dd style="display: inline-block;">{{$item->property}}</dd>
                                                        {{--
                                                        <dt>Ghi chú sản phẩm</dt>
                                                        <dd>
                                                             <input
                                                                 data-toggle="_tooltip"
                                                                 data-shop-id="{{$shop->shop_id}}"
                                                                 data-item-id="{{$item->id}}"
                                                                 placeholder="Ghi chú sản phẩm..."
                                                                 name="comment"
                                                                 type="text"
                                                                 data-url="{{ url('gio-hang/hanh-dong') }}"
                                                                 data-method="post"
                                                                 data-key-global="cart-item-comment-{{$shop->shop_id}}-{{$item->id}}"
                                                                 class="form-control __input-comment-item" value="{{$item->comment}}" />
                                                        </dd>
                                                        --}}
                                                    </dl>

                                                    <div class="m-t-sm">
                                                        <form class="___form">
                                                            <input type="hidden" name="action" value="remove_item">
                                                            <input type="hidden" name="method" value="post">
                                                            <input type="hidden" name="shop_id" value="{{$shop->shop_id}}">
                                                            <input type="hidden" name="item_id" value="{{$item->id}}">
                                                            <input type="hidden" name="url" value="{{ url('gio-hang/hanh-dong') }}">
                                                            <input type="hidden" name="response" value="onicustomer/cart">
                                                            <input type="hidden" name="_token" value="{{ csrf_token()  }}">

                                                            <a class="__removeItem text-muted"
                                                               href="javascript:void(0)"
                                                               data-toggle="tooltip"
                                                               title="Xoá sản phẩm">
                                                                <i class="fa fa-trash"></i> Xoá sản phẩm
                                                            </a>
                                                        </form>
                                                    </div>
                                                </td>
                                                <td width="100">
                                                    <span class="price_vnd">{{ App\Util::formatNumber($item->price_calculator_vnd) }}</span><sup>đ</sup>
                                                    <p class="small text-muted">¥<span class="price">{{$item->price_calculator}}</span></p>
                                                </td>
                                                <td width="50">
                                                    <form class="___form" onsubmit="return false;">
                                                        <input type="hidden" name="action" value="update_quantity">
                                                        <input type="hidden" name="method" value="post">
                                                        <input type="hidden" name="shop_id" value="{{$shop->shop_id}}">
                                                        <input type="hidden" name="item_id" value="{{$item->id}}">
                                                        <input type="hidden" name="url" value="{{ url('gio-hang/hanh-dong') }}">
                                                        <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                                        <input type="hidden" name="response" value="onicustomer/cart">

                                                        <input
                                                                data-toggle="_tooltip"
                                                                style="min-width: 60px"
                                                                type="number"
                                                                data-shop-id="{{$shop->shop_id}}"
                                                                data-shop-item-id="{{$item->id}}"
                                                                data-key-global="cart-item-quantity-{{$shop->shop_id}}-{{$item->id}}"
                                                                name="quantity"
                                                                class="form-control text-center touchspin __changeQty" value="{{$item->quantity}}" />
                                                    </form>
                                                </td>
                                                <td width="100">
                                                    <strong>
                                                        <span class="sub_total_vnd">{{ App\Util::formatNumber($item->total_amount_item_vnd)  }}</span><sup>đ</sup>
                                                    </strong>
                                                    <p class="small text-muted">¥<span class="sub_total">{{$item->total_amount_item}}</span></p>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </table>
                                </div>
                            </div>
                            <div class="ibox-content clearfix">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h3>Dịch vụ:</h3>

                                        @foreach($data['services'] as $service)
                                            <div class="">
                                                <form class="___form">
                                                    <input type="hidden" name="action" value="choose_service">
                                                    <input type="hidden" name="method" value="post">
                                                    <input type="hidden" name="shop_id" value="{{$shop->shop_id}}">
                                                    <input type="hidden" name="url" value="{{ url('gio-hang/hanh-dong') }}">
                                                    <input type="hidden" name="response" value="customer/cart">
                                                    <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                                    <input type="hidden" name="service" value="{{$service['code']}}">

                                                    <div class="checkbox m-r-xs">
                                                        <input
                                                            @if(in_array($service['code'], $shop->services)) checked @endif
                                                            type="checkbox"
                                                            value="{{$service['code']}}"
                                                            class="___btn-action"
                                                            id="checkbox_{{$service['code']}}_{{$shop->id}}">

                                                        <label for="checkbox_{{$service['code']}}_{{$shop->id}}">
                                                            {{$service['title']}}
                                                        </label>
                                                    </div>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col-sm-6">
                                        <table class="table invoice-total">
                                            <tbody>
                                            <tr>
                                                <td><strong>Tiền hàng :</strong></td>
                                                <td style="min-width: 100px;">
                                                    <span class="shop_total_vnd">{{ App\Util::formatNumber($shop->total_amount_items)  }}</span><sup>đ</sup>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Mua hàng :</strong></td>
                                                <td style="min-width: 100px;">
                                                    <span class="shop_buying_fee">{{ App\Util::formatNumber($shop->buying_fee)  }}</span><sup>đ</sup>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>
                                                        <i data-toggle="tooltip" title="Khi NhatMinh247 nhận hàng của bạn, lúc đó hệ thống sẽ tính toán phí này. Hiện giờ chưa rõ cân nặng để tính phí" class="fa fa-question-circle"></i>
                                                        Vận chuyển TQ - VN:
                                                    </strong>
                                                </td>
                                                <td style="min-width: 100px;">0<sup>đ</sup></td>
                                            </tr>

                                            @if($shop->checkExistsCartService(App\Service::TYPE_WOOD_CRATING))
                                                <tr>
                                                    <td><strong><i data-toggle="tooltip" title="Khi NhatMinh247 nhận hàng của bạn, lúc đó hệ thống sẽ tính toán phí này. Hiện giờ chưa rõ cân nặng để tính phí" class="fa fa-question-circle"></i> Đóng gỗ:</strong></td>
                                                    <td style="min-width: 100px;">{{ App\Util::formatNumber($shop->wood_crating)  }}<sup>đ</sup></td>

                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="pull-right">
                                    <!-- button Xoa cua hang -->
                                    <form class="___form" style="display: inline-block;">
                                        <input type="hidden" name="action" value="remove_shop">
                                        <input type="hidden" name="method" value="post">
                                        <input type="hidden" name="shop_id" value="{{$shop->shop_id}}">
                                        <input type="hidden" name="url" value="{{ url('gio-hang/hanh-dong') }}">
                                        <input type="hidden" name="confirm" value="Bạn có chắc muốn xoá shop này?">
                                        <input type="hidden" name="response" value="customer/cart">
                                        <input type="hidden" name="_token" value="{{ csrf_token()  }}">

                                        <!--___btn-action-->
                                        <a href="javascript:void(0)"
                                           class="btn btn-link __removeShop"
                                           data-toggle="tooltip"
                                           data-shop-id="{{$shop->shop_id}}"
                                           title="Xoá shop">
                                            <i class="fa fa-trash-o"></i> Xóa shop
                                        </a>
                                    </form>
                                    <!--/ button Xoa cua hang -->

                                    <!-- button dat coc -->
                                    <a href="{{ url('dat-coc?shop_id=' . $shop->shop_id)  }}" class="btn btn-danger">
                                        <i class="fa fa-shopping-cart"></i> Đặt cọc
                                    </a>
                                    <!--/ button dat coc -->

                                    <!-- button Update --
                                    <a href="javascript:void(0)" class="btn btn-info">
                                        <i class="fa fa-floppy-o" aria-hidden="true"></i> Cập nhật
                                    </a>
                                    <!--/ button Update -->
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center m-t-lg">
                                <i class="fa fa-shopping-cart fa-5x" aria-hidden="true"></i>
                                <h1>Giỏ hàng hiện đang trống!</h1>
                                <small>Vui lòng chọn sản phẩm và tiến hành đặt hàng.</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-md-3">
                <!-- data-spy="affix" data-offset-top="11" data-offset-bottom="64" -->
                <div class="cart_sidebar">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Thông tin giỏ hàng</h5>
                        </div>
                        <div class="ibox-content">
                            <span>Cửa hàng</span>
                            <h2 class="font-bold">
                                <span class="shops_count">{{$data['statistic']['total_shops']}}</span>
                            </h2>
                            <span>Sản phẩm</span>
                            <h2 class="font-bold">
                                <span class="cart_qty">{{$data['statistic']['total_items']}}</span>
                            </h2>
                            <span>Tiền hàng</span>
                            <h2 class="font-bold">
                                <span class="cart_total">{{ App\Util::formatNumber($data['statistic']['total_amount'])  }}</span> <sup>đ</sup>
                            </h2>

                            <hr>
                            <span class="text-muted small">
                                <ul>
                                    <li>Đây là những sản phẩm hiện tại có trong giỏ hàng của bạn.</li>
                                    <li>Bạn có thể thay đổi sản phẩm hoặc xóa sản phẩm nếu không muốn đặt nữa.</li>
                                    <li>Tiền hàng ở trên chưa bao gồm chi phí vận chuyển và các dịch vụ khác.</li>
                                </ul>
                            </span>
                            <!--
                            <div class="m-t-sm">
                                <div class="btn-group btn-group-justified">
                                    <a href="#" class="btn btn-primary btn-sm"><i class="fa fa-shopping-cart"></i>  ĐẶT CỌC</a>
                                    <a href="#" class="btn btn-white btn-sm"> XÓA TẤT CẢ </a>
                                </div>
                            </div>
                            -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('header-scripts')
    <link href="{!! asset('oniasset/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') !!}" rel="stylesheet"/>
    <link href="{!! asset('oniasset/css/plugins/touchspin/jquery.bootstrap-touchspin.min.css') !!}" rel="stylesheet"/>
@endsection

@section('footer-scripts')
    <!-- TouchSpin -->
    <script src="{!! asset('oniasset/js/plugins/touchspin/jquery.bootstrap-touchspin.min.js') !!}"></script>
    <script>
        $(document).ready(function(){
            $('.__changeQty').TouchSpin({
                min: 0,
                max: 10000,
                buttondown_class: 'btn btn-white',
                buttonup_class: 'btn btn-white'
            });
        });
    </script>
@endsection