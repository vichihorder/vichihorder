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
                'icon' => 'fa fa-tachometer',
                'title' => 'Bảng tin',
                'key_active' => 'home',
            ],
            [
                'url' => url('don-hang'),
                'icon' => 'fa fa-cubes',
                'title' => 'Đơn hàng',
                'key_active' => 'don-hang',
            ],
            [
                'url' => url('giao-dich'),
                'icon' => 'fa fa-money',
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

<!-- Left Sidebar Start -->
<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <!-- Search form -->
        <form role="search" class="navbar-form">
            <div class="form-group">

            </div>
        </form>
        <div class="clearfix"></div>

        <div id="sidebar-menu">
            <ul>
                <?php if(count($menus)){ array_splice($menus, 0, 1); ?>
                <?php foreach($menus as $key => $menu){ ?>

                <?php if(empty($menu['children'])){ ?>
                <?php
                if(!empty($menu['permission']) && !App\Permission::isAllow($menu['permission'])){
                    continue;
                }
                ?>
                <li>
                    <a href="{{$menu['url']}}" class="{{ Request::segment(1) === $menu['key_active'] ? 'active' : null }}">
                        <i class="{{$menu['icon']}}"></i>
                        <span>{{$menu['title']}}</span>
                    </a>
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
        </div>
    </div>

</div>
<!-- Left Sidebar End -->