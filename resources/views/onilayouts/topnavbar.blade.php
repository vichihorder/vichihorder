<!-- Top Bar Start -->
<div class="topbar">
    <div class="topbar-left">
        <div class="logo">
            <h1><a href="{{url('')}}"><img src="{{ asset('oniasset/img/logo.png') }}" alt="Logo"></a></h1>
        </div>
        <button class="button-menu-mobile open-left">
            <i class="fa fa-bars"></i>
        </button>
    </div>
    <!-- Button mobile view to collapse sidebar menu -->
    <div class="navbar navbar-default" role="navigation">
        <div class="container">
            <div class="navbar-collapse2">
                <ul class="nav navbar-nav hidden-xs">
                    <li>
                        <p class="navbar-text">Tỉ giá: {{ number_format(App\Exchange::getExchange(), 0, ",", ".")  }} <sup>đ</sup></p>
                    </li>
                    <li>
                        <p class="navbar-text">Số dư: <strong class="text-danger"><?php echo App\Util::formatNumber(Auth::user()->account_balance) ?> <sup>đ</sup></strong></p>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right top-navbar">
                    <li class="iconify hide-phone">
                        <a href="{{ url('gio-hang') }}">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="label label-danger absolute">{{ App\Cart::getCartTotalQuantityItem(Auth::user()->id)  }}</span>
                        </a>

                    </li>
                    <li class="dropdown iconify hide-phone">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-globe"></i><span class="label label-danger absolute">4</span></a>
                        <ul class="dropdown-menu dropdown-message">
                            <li class="dropdown-header notif-header"><i class="icon-bell-2"></i> New Notifications<a class="pull-right" href="#"><i class="fa fa-cog"></i></a></li>
                            <li class="unread">
                                <a href="#">
                                    <p><strong>John Doe</strong> Uploaded a photo <strong>&#34;DSC000254.jpg&#34;</strong>
                                        <br /><i>2 minutes ago</i>
                                    </p>
                                </a>
                            </li>
                            <li class="unread">
                                <a href="#">
                                    <p><strong>John Doe</strong> Created an photo album  <strong>&#34;Fappening&#34;</strong>
                                        <br /><i>8 minutes ago</i>
                                    </p>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <p><strong>John Malkovich</strong> Added 3 products
                                        <br /><i>3 hours ago</i>
                                    </p>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <p><strong>Sonata Arctica</strong> Send you a message <strong>&#34;Lorem ipsum dolor...&#34;</strong>
                                        <br /><i>12 hours ago</i>
                                    </p>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <p><strong>Johnny Depp</strong> Updated his avatar
                                        <br /><i>Yesterday</i>
                                    </p>
                                </a>
                            </li>
                            <li class="dropdown-footer">
                                <div class="btn-group btn-group-justified">
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-primary"><i class="icon-ccw-1"></i> Refresh</a>
                                    </div>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-danger"><i class="icon-trash-3"></i> Clear All</a>
                                    </div>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-success">See All <i class="icon-right-open-2"></i></a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown topbar-profile">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span class="rounded-image topbar-profile-image">
                                <img src="{{ asset('images/default-avatar.png') }}">
                            </span> {{ Auth::user()->name }} <i class="fa fa-caret-down"></i>
                        </a>
                        <?php
                        $current_user = App\User::find(Auth::user()->id);
                        ?>
                        <ul class="dropdown-menu">
                            <li>
                                @if(Auth::user()->section == App\User::SECTION_CRANE)
                                    <a href="{{ url('user/detail', Auth::user()->id)  }}">
                                @else
                                    <a href="{{ url('nhan-vien', Auth::user()->id)  }}">
                                @endif
                                    <span class="fa fa-user"></span>
                                        Thông tin cá nhân
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('san-pham-da-luu')  }}">
                                    <span class="fa fa-bookmark"></span>
                                    Sản phẩm đã lưu
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('giao-dich')  }}">
                                    <span class="fa fa-exchange"></span>
                                    Lịch sử Giao dịch
                                </a>
                            </li>
                            <li class="divider"></li>
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
                    </li>
                    {{--<li class="right-opener">
                        <a href="javascript:;" class="open-right"><i class="fa fa-angle-double-left"></i><i class="fa fa-angle-double-right"></i></a>
                    </li>--}}
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </div>
</div>
<!-- Top Bar End -->