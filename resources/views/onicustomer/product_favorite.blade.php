@extends($layout)

@section('page_title')
    {{$page_title}}
@endsection

@section('content')
        <div class="ibox">
            <div class="ibox-content">
                @if($total_product_favorite)
                    <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="15">
                        <thead>
                        <tr>
                            <th data-sort-ignore="true" class="text-center">Hình ảnh</th>
                            <th data-sort-ignore="true" data-hide="phone">Sản phẩm</th>
                            <th data-hide="phone" class="text-center">Website</th>
                            <th class="text-center">Ngày lưu</th>
                            <!--<th data-sort-ignore="true" class="text-center">Action</th>-->
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($product_favorite as $key => $product_favorite_item)
                        <tr>
                            <td width="90">
                                <div class="cart-product-imitation" style="padding-top: 0;">
                                    <img src="{{$product_favorite_item->avatar}}" style="width: 100%; height: 100%;">
                                </div>
                            </td>
                            <td class="desc">
                                <h3>
                                    <a href="{{$product_favorite_item->link}}" target="_blank" title="Đễn trang sản phẩm">
                                        <i class="fa fa-external-link" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{$product_favorite_item->link}}" target="_blank" class="text-navy">
                                        {{$product_favorite_item->product_name}}
                                    </a>
                                </h3>
                            </td>
                            <td width="100" class="text-center">
                                {!! App\Util::showSite($product_favorite_item->site) !!}
                            </td>
                            <td width="100" class="text-center" data-value="{{$product_favorite_item->updated_at}}">
                                {{ App\Util::formatDate($product_favorite_item->updated_at)  }}
                            </td>
                            <!--
                            <td width="100" class="text-center">
                                <div class="btn-group">
                                    <a href="{{$product_favorite_item->link}}" class="btn-white btn btn-xs" target="_blank">Chi tiết</a>
                                    <a href="#" class="btn-white btn btn-xs">Xóa</a>
                                </div>
                            </td>
                            -->
                        </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4">
                                <ul class="pagination pull-right"></ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                    {{ $product_favorite->links() }}
                @else
                    <h3>Hiện tại bạn chưa lưu sản phẩm nào!</h3>
                @endif
            </div>
        </div>
@endsection

@section('header-scripts')
    <link href="{!! asset('oniasset/css/plugins/footable/footable.core.css') !!}" rel="stylesheet"/>
@endsection

@section('footer-scripts')
    <script src="{{ asset('oniasset/js/plugins/footable/footable.all.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.footable').footable();
        });
    </script>
@endsection

