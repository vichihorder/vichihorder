<?php
$menus = [];

if(Auth::check()){
    if(Auth::user()->section == App\User::SECTION_CUSTOMER){
        $menus = [
            [
                'url' => url(''),
                'icon' => 'fa fa-home',
                'title' => 'Trang chủ',
                'key_active' => '',
            ],

            [
                'url' => url('home'),
                'icon' => 'fa fa-tasks',
                'title' => 'Bảng chung',
                'key_active' => 'home',
            ],
            [
                'url' => url('don-hang'),
                'icon' => 'fa-cubes',
                'title' => 'Đơn hàng',
                'key_active' => 'don-hang',
            ],
            [
                'url' => url('giao-dich'),
                'icon' => 'fa-money',
                'title' => 'Giao dịch',
                'key_active' => 'giao-dich',
            ],
        ];
    }else{
        $menus = [
            [
                'url' => url(''),
                'icon' => 'fa fa-home',
                'title' => 'Trang chủ',
                'key_active' => '',
            ],
            [
                'url' => url('home'),
                'icon' => 'fa fa-tasks',
                'title' => 'Bảng chung',
                'key_active' => 'home',
            ],
            [
                'url' => '#',
                'icon' => 'fa-heartbeat',
                'title' => 'Vận Hành',
                'key_active' => '',
                'children' => [
                    [
                        'url' => url(sprintf('order')),
                        'title' => 'Đơn hàng',
                        'permission' => \App\Permission::PERMISSION_ORDER_LIST_VIEW
                    ],
                    [
                        'url' => url(sprintf('order_buying?status=%s', App\Order::STATUS_DEPOSITED)),
                        'title' => 'Mua hàng',
                        'permission' => \App\Permission::PERMISSION_ORDER_BUYING_LIST_VIEW
                    ],
                    [
                        'url' => url('packages'),
                        'title' => 'Kiện hàng',
                        'permission' => \App\Permission::PERMISSION_PACKAGE_LIST_VIEW
                    ],
                    [
                        'url' => url('package'),
                        'title' => 'Tạo kiện',
                        'permission' => \App\Permission::PERMISSION_PACKAGE_ADD
                    ],
                    [
                        'url' => url('scan'),
                        'title' => 'Quét mã vạch',
                        'permission' => \App\Permission::PERMISSION_SCAN_LIST_VIEW
                    ],

                    [
                        'url' => url('DeliveryManage'),
                        'title' => 'Yêu cầu giao hàng',
                        'permission' => \App\Permission::PERMISSION_DELIVERY_MANAGE_LIST_VIEW
                    ],
                    [
                        'url' => url('BillManage'),
                        'title' => 'Phiếu giao hàng',
                        'permission' => \App\Permission::PERMISSION_BILL_MANAGE_LIST_VIEW
                    ],
                    [
                        'url' => url('SystemRunCheck'),
                        'title' => 'Kiểm soát vận hành',
                        'permission' => \App\Permission::PERMISSION_SYSTEM_RUN_CHECK
                    ],
                ]
            ],
            [
                'url' => '#',
                'icon' => 'fa-user',
                'title' => 'Nhân viên',
                'key_active' => '',
                'children' => [
                    [
                        'url' => url('user'),
                        'title' => 'Quản lý nhân viên',
                        'permission' => \App\Permission::PERMISSION_USER_VIEW_LIST
                    ],
                ]
            ],
            [
                'url' => '#',
                'icon' => 'fa-money',
                'title' => 'Tài chính',
                'key_active' => '',
                'children' => [
                    [
                        'url' => url('transactions'),
                        'title' => 'Lịch sử giao dịch',
                        'permission' => \App\Permission::PERMISSION_TRANSACTION_VIEW
                    ],
                    [
                        'url' => url('transaction/adjustment'),
                        'title' => 'Tạo điều chỉnh tài chính',
                        'permission' => \App\Permission::PERMISSION_TRANSACTION_CREATE
                    ],
                    [
                        'url' => url('accouting_finance'),
                        'title' => 'Thông kê tài chính khách nợ',
                        'permission' => \App\Permission::PERMISSION_CUSTOMER_WITHOUT
                    ],
                    [
                        'url' => url('san-luong-van-chuyen'),
                        'title' => 'Báo cáo Thống kê',
                        'permission' => \App\Permission::PERMISSION_MANAGER_ADDON_LINK_ERROR
                    ],
                    [
                        'url' => url('PaidStaffSaleValue'),
                        'title' => 'Doanh số, lương mua hàng',
                        'permission' => \App\Permission::PERMISSION_PAID_STAFF_SALE_VALUE
                    ],

                ]
            ],
            [
                'url' => '#',
                'icon' => 'fa-newspaper-o',
                'title' => 'Tin tức',
                'key_active' => '',
                'children' => [
                    [
                        'url' => url('taxonomies'),
                        'title' => 'Quản lý nhóm tin',
                        'permission' => \App\Permission::PERMISSION_MANAGER_TAXONOMY
                    ],
                    [
                        'url' => url('taxonomy'),
                        'title' => 'Tạo nhóm tin',
                        'permission' => \App\Permission::PERMISSION_MANAGER_TAXONOMY
                    ],
                    [
                        'url' => url('posts'),
                        'title' => 'Quản lý tin tức',
                        'permission' => \App\Permission::PERMISSION_MANAGER_POST
                    ],
                    [
                        'url' => url('post'),
                        'title' => 'Tạo tin tức',
                        'permission' => \App\Permission::PERMISSION_MANAGER_POST
                    ],
                ]
            ],
            [
                'url' => '#',
                'icon' => 'fa-gear',
                'title' => 'Hệ thống',
                'key_active' => '',
                'children' => [

                    [
                        'url' => url('setting/roles'),
                        'title' => 'Nhóm & phân quyền',
                        'permission' => \App\Permission::PERMISSION_VIEW_LIST_ROLE
                    ],
                    [
                        'url' => url('user/original_site'),
                        'title' => 'Quản lý user mua hàng site gốc',
                        'permission' => \App\Permission::PERMISSION_MANAGER_USER_ORIGINAL_SITE
                    ],
                    [
                        'url' => url('warehouses'),
                        'title' => 'Quản lý kho hàng',
                        'permission' => \App\Permission::PERMISSION_MANAGER_WAREHOUSE
                    ],
                    [
                        'url' => url('warehouses_manually'),
                        'title' => 'Cấu hình kho',
                        'permission' => \App\Permission::PERMISSION_MANAGER_WAREHOUSE_MANUALLY_VIEW
                    ],
                    [
                        'url' => url('setting'),
                        'title' => 'Cấu hình chung',
                        'permission' => \App\Permission::PERMISSION_UPDATE_SYSTEM_CONFIG
                    ],
                    [
                        'url' => url('manager_addon_link_error'),
                        'title' => 'Quản lý link đặt hàng báo lỗi',
                        'permission' => \App\Permission::PERMISSION_MANAGER_ADDON_LINK_ERROR
                    ],

                ]
            ]
        ];


    }
}
?>

<div class="row border-bottom white-bg">
    <nav class="navbar navbar-fixed-top" role="navigation">
        <div class="navbar-header">
            <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                <i class="fa fa-reorder"></i>
            </button>

            <button class="navbar-toggle visible-xs right-sidebar-toggle">
                <i class="fa fa-comments-o"></i>
            </button>
            <a href="{{url('')}}" class="navbar-brand">NhatMinh247</a>
        </div>

        <div class="navbar-collapse collapse" id="navbar">
            <ul class="nav navbar-nav">
                <?php if(count($menus)){ array_splice($menus, 0, 1); ?>
                <?php foreach($menus as $key => $menu){ ?>

                <?php if(empty($menu['children'])){ ?>
                <?php
                if(!empty($menu['permission']) && !App\Permission::isAllow($menu['permission'])){
                    continue;
                }
                ?>

                <li class="{{ Request::segment(1) === $menu['key_active'] ? 'active' : null }}">
                    <a href="{{$menu['url']}}">{{$menu['title']}}</a>
                </li>
                <?php } else { ?>

                <li class="dropdown">
                    <a aria-expanded="false" role="button" href="{{$menu['url']}}" class="dropdown-toggle" data-toggle="dropdown"> {{$menu['title']}} <span class="caret"></span></a>

                    <?php
                    $html = [];
                    foreach($menu['children'] as $key_children => $menu_children){
                        if(!empty($menu_children['permission']) && !App\Permission::isAllow($menu_children['permission'])){
                            continue;
                        }
                        $html[] = sprintf('<li><a href="%s">%s</a></li>', $menu_children['url'], $menu_children['title']);
                    } ?>
                    <?php
                    if(count($html)){
                        echo sprintf('<ul role="menu" class="dropdown-menu">%s</ul>', implode('', $html));
                    }
                    ?>
                </li>

                <?php } ?>
                <?php } ?>
                <?php } ?>
            </ul>
            <ul class="nav navbar-top-links navbar-right">

                @if(!Auth::user()->section == App\User::SECTION_CRANE)
                <li>
                    <span class="m-r-sm text-muted">Tỉ giá: {{ number_format(App\Exchange::getExchange(), 0, ",", ".")  }} <sup>đ</sup></span>
                </li>
                <li>
                    <span class="m-r-sm text-muted">Số dư: <strong class="text-danger"><?php echo App\Util::formatNumber(Auth::user()->account_balance) ?> <sup>đ</sup></strong></span>
                </li>
                @endif
                <li>
                    <a class="count-info" href="{{ url('gio-hang') }}">
                        <i class="fa fa-shopping-basket"></i>  <span class="label label-warning">{{ App\Cart::getCartTotalQuantityItem(Auth::user()->id)  }}</span>
                    </a>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell"></i>  <span class="label label-primary">8</span>
                    </a>
                    <ul class="dropdown-menu dropdown-alerts">
                        <li>
                            <a href="mailbox.html">
                                <div>
                                    <i class="fa fa-envelope fa-fw"></i> You have 16 messages
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="profile.html">
                                <div>
                                    <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                                    <span class="pull-right text-muted small">12 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="grid_options.html">
                                <div>
                                    <i class="fa fa-upload fa-fw"></i> Server Rebooted
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <div class="text-center link-block">
                                <a href="notifications.html">
                                    <strong>See All Alerts</strong>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a class="count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-user-circle-o" aria-hidden="true"></i> {{ Auth::user()->name }}
                    </a>
                    <div class="dropdown-menu dropdown-user animated fadeInRight m-t-xs">
                        <?php
                        $current_user = App\User::find(Auth::user()->id);
                        ?>
                        <div class="contact-box">
                            <div href="#">
                                <div class="col-sm-4">
                                    <div class="text-center">
                                        <img alt="image" class="img-circle m-t-xs img-responsive" src="{{ asset('images/default-avatar.png') }}">
                                        <div class="m-t-xs font-bold"></div>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <h3 class="m-b-none"><strong>{{ Auth::user()->name }}</strong></h3>
                                    <p>{{App\User::getSectionName($current_user->section)}} <strong>#{{ $current_user->code }}</strong></p>
                                    <ul class="user-links list-unstyled m-l-n-xs m-t-md">
                                        <li>
                                            @if(Auth::user()->section == App\User::SECTION_CRANE)
                                                <a href="{{ url('user/detail', Auth::user()->id)  }}">
                                            @else
                                                 <a href="{{ url('nhan-vien', Auth::user()->id)  }}">
                                            @endif
                                                 <span class="fa fa-address-book m-r-xs"></span>
                                                 Thông tin cá nhân
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ url('san-pham-da-luu')  }}">
                                                <span class="fa fa-bookmark m-r-xs"></span>
                                                Sản phẩm đã lưu
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ url('giao-dich')  }}">
                                                <span class="fa fa-exchange m-r-xs"></span>
                                                Lịch sử Giao dịch
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                <span class="fa fa-power-off m-r-xs"></span>
                                                Thoát
                                            </a>

                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <!--
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="{{ url('wishlist')  }}">Sản phẩm đã lưu</a></li>
                        <li>
                            @if(Auth::user()->section == App\User::SECTION_CRANE)
                                <a href="{{ url('user/detail', Auth::user()->id)  }}">
                                    @else
                                        <a href="{{ url('user', Auth::user()->id)  }}">
                                            @endif
                                            Thông tin cá nhân
                                        </a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Thoát
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                    -->
                </li>
            </ul>
        </div>
    </nav>
</div>
