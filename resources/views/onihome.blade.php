@extends($layout)

@section('page_title')
    {{$page_title}}
@endsection

@section('content')
    <div class="wrapper wrapper-content">
    @if($current_user->section == App\User::SECTION_CRANE)
        <div class="row">
            <div class="col-sm-4">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="widget style1 navy-bg">
                            <div class="row">
                                <div class="col-xs-4">
                                    <i class="fa fa-shopping-basket fa-5x"></i>
                                </div>
                                <div class="col-xs-8 text-right">
                                    <span> Đơn đặt cọc trong ngày </span>
                                    <h2 class="font-bold">{{$total_order_deposit_today}}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="widget style1 lazur-bg">
                            <div class="row">
                                <div class="col-xs-4">
                                    <i class="fa fa-user-plus fa-5x"></i>
                                </div>
                                <div class="col-xs-8 text-right">
                                    <span> Khách đăng ký trong ngày </span>
                                    <h2 class="font-bold">{{$total_customer_register_today}}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($permission['can_view_statistic_money_quick'])
                <div class="col-sm-8 col-xs-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Thống kê tài chính</h5>
                        </div>
                        <div class="ibox-content">
                            <div style="text-transform: none;    font-size: 14px;">
                                <form action="" onsubmit="return false;" class="_filter-statistic">
                                    Từ
                                    <input name="start_date" type="date" data-date="" data-date-format="DD MMMM YYYY" value="{{ sprintf('%s', date('Y-m-d'))  }}">
                                    đến
                                    <input name="end_date" type="date" data-date="" data-date-format="DD MMMM YYYY" value="{{ sprintf('%s', date('Y-m-d'))  }}">
                                </form>
                                <p style="color: grey; font-size: 12px;">Định dạng: tháng/ngày/năm</p>

                            </div>
                            <div id="_home-statistic">

                            </div>

                            <ul class="card-action">
                                @if($permission['can_view_statistic_money_detail'])
                                    <li>
                                        <a href="{{ url('statistic/users')  }}" style="color: #5e6263;">
                                            <small>Xem chi tiết >></small>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                <!--
                    <div class="card card-mini hidden" style="margin-top: 30px;">
                        <div class="card-header">
                            <div class="card-title text-uppercase">
                                Thống kê mua hàng
                                <br>
                                <div style="text-transform: none;
    font-size: 14px;">
                                    <form action="" onsubmit="return false;" class="_filter-statistic-order-buying">
                                        Từ
                                        <input name="start_date" type="date" data-date="" data-date-format="DD MMMM YYYY" value="{{ sprintf('%s', date('Y-m-d'))  }}">
                                        đến
                                        <input name="end_date" type="date" data-date="" data-date-format="DD MMMM YYYY" value="{{ sprintf('%s', date('Y-m-d'))  }}">
                                    </form>
                                    <p style="color: grey; font-size: 12px;">Định dạng: tháng/ngày/năm</p>

                                </div>
                            </div>

                        </div>
                        <div class="card-body no-padding table-responsive" id="_home-statistic-order-buying"></div>
                    </div>
-->
                </div>
            @endif
        </div>
    @endif

    <div class="row">
        <div class="col-sm-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Hướng dẫn dành cho khách hàng mới</h5>
                </div>
                <div>
                    <div class="ibox-content">
                        <div class="feed-activity-list">
                            <div class="feed-element">
                                <i class="fa fa-cogs" aria-hidden="true"></i> <a class="text-info" href="{{ url('ho-tro', 4)  }}">Hướng dẫn cài đặt công cụ đặt hàng & đặt cọc đơn hàng</a>
                            </div>
                            <div class="feed-element">
                                <i class="fa fa-search" aria-hidden="true"></i> <a class="text-info" href="{{ url('ho-tro', 5)  }}">Hướng dẫn tìm nguồn hàng trên website taobao.com, tmall.com, 1688.com</a>
                            </div>
                            <div class="feed-element">
                                <i class="fa fa-money" aria-hidden="true"></i> <a class="text-info" href="{{ url('ho-tro', 1)  }}">Hướng dẫn nạp tiền vào tài khoản</a>
                            </div>
                            <div class="feed-element">
                                <i class="fa fa-list-alt" aria-hidden="true"></i> <a class="text-info" href="{{ url('ho-tro', 2)  }}">Xem biểu phí</a>
                            </div>
                        </div>
                        <a href="{{ url('')  }}" class="btn btn-primary btn-block m-t"><i class="fa fa-reply" aria-hidden="true"></i> Về trang chủ</a>
                    </div>
                </div>
            </div>
        </div>
<!--
        <div class="col-sm-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Đơn hàng gần đây</h5>
                </div>
                <div>
                    <div class="ibox-content">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Data</th>
                                <th>User</th>
                                <th>Value</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td><span class="line" style="display: none;">5,3,2,-1,-3,-2,2,3,5,2</span><svg class="peity" height="16" width="32"><polygon fill="#1ab394" points="0 9.375 0 0.5 3.5555555555555554 4.25 7.111111111111111 6.125 10.666666666666666 11.75 14.222222222222221 15.5 17.77777777777778 13.625 21.333333333333332 6.125 24.888888888888886 4.25 28.444444444444443 0.5 32 6.125 32 9.375"></polygon><polyline fill="transparent" points="0 0.5 3.5555555555555554 4.25 7.111111111111111 6.125 10.666666666666666 11.75 14.222222222222221 15.5 17.77777777777778 13.625 21.333333333333332 6.125 24.888888888888886 4.25 28.444444444444443 0.5 32 6.125" stroke="#169c81" stroke-width="1" stroke-linecap="square"></polyline></svg></td>
                                <td>Samantha</td>
                                <td class="text-navy"> <i class="fa fa-level-up"></i> 40% </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><span class="line" style="display: none;">5,3,9,6,5,9,7,3,5,2</span><svg class="peity" height="16" width="32"><polygon fill="#1ab394" points="0 15 0 7.166666666666666 3.5555555555555554 10.5 7.111111111111111 0.5 10.666666666666666 5.5 14.222222222222221 7.166666666666666 17.77777777777778 0.5 21.333333333333332 3.833333333333332 24.888888888888886 10.5 28.444444444444443 7.166666666666666 32 12.166666666666666 32 15"></polygon><polyline fill="transparent" points="0 7.166666666666666 3.5555555555555554 10.5 7.111111111111111 0.5 10.666666666666666 5.5 14.222222222222221 7.166666666666666 17.77777777777778 0.5 21.333333333333332 3.833333333333332 24.888888888888886 10.5 28.444444444444443 7.166666666666666 32 12.166666666666666" stroke="#169c81" stroke-width="1" stroke-linecap="square"></polyline></svg></td>
                                <td>Jacob</td>
                                <td class="text-warning"> <i class="fa fa-level-down"></i> -20% </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><span class="line" style="display: none;">1,6,3,9,5,9,5,3,9,6,4</span><svg class="peity" height="16" width="32"><polygon fill="#1ab394" points="0 15 0 13.833333333333334 3.2 5.5 6.4 10.5 9.600000000000001 0.5 12.8 7.166666666666666 16 0.5 19.200000000000003 7.166666666666666 22.400000000000002 10.5 25.6 0.5 28.8 5.5 32 8.833333333333332 32 15"></polygon><polyline fill="transparent" points="0 13.833333333333334 3.2 5.5 6.4 10.5 9.600000000000001 0.5 12.8 7.166666666666666 16 0.5 19.200000000000003 7.166666666666666 22.400000000000002 10.5 25.6 0.5 28.8 5.5 32 8.833333333333332" stroke="#169c81" stroke-width="1" stroke-linecap="square"></polyline></svg></td>
                                <td>Damien</td>
                                <td class="text-navy"> <i class="fa fa-level-up"></i> 26% </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><span class="line" style="display: none;">1,6,3,9,5,9,5,3,9,6,4</span><svg class="peity" height="16" width="32"><polygon fill="#1ab394" points="0 15 0 13.833333333333334 3.2 5.5 6.4 10.5 9.600000000000001 0.5 12.8 7.166666666666666 16 0.5 19.200000000000003 7.166666666666666 22.400000000000002 10.5 25.6 0.5 28.8 5.5 32 8.833333333333332 32 15"></polygon><polyline fill="transparent" points="0 13.833333333333334 3.2 5.5 6.4 10.5 9.600000000000001 0.5 12.8 7.166666666666666 16 0.5 19.200000000000003 7.166666666666666 22.400000000000002 10.5 25.6 0.5 28.8 5.5 32 8.833333333333332" stroke="#169c81" stroke-width="1" stroke-linecap="square"></polyline></svg></td>
                                <td>Damien</td>
                                <td class="text-navy"> <i class="fa fa-level-up"></i> 26% </td>
                            </tr>
                            </tbody>
                        </table>


                        <a href="{{ url('')  }}" class="btn btn-primary btn-block m-t">Xem thêm</a>
                    </div>
                </div>
            </div>
        </div>
-->
    </div>
    </div>
@endsection

@section('header-scripts')
@endsection


@section('footer-scripts')
    @parent
    <script src="{{ asset('js/jquery.animateNumber.js')  }}"></script>
    <script>
        $(document).ready(function(){
            /* thong ke tai chinh */
            $(document).on('change', '._filter-statistic', function(){
                var data = $(this).serializeObject();
                home_statistic(data);
            });

            function home_statistic(send_data){
                request('home/statistic', 'get', send_data).done(function(response){
                    if(response.success){
                        $('#_home-statistic').html(response.html);
                        var comma_separator_number_step = $.animateNumber.numberStepFactories.separator('.');
                        $('.lines').each(function(i){
                            $(this).animateNumber({ number: $(this).data('money'), numberStep: comma_separator_number_step });
                        });

                    }else{
                        bootbox.alert(response.message);
                    }
                });
            }

            var can_view_home_statistic = "{{ $permission['can_view_statistic_money_quick'] }}";

            if(can_view_home_statistic == 1){
                var data = $('._filter-statistic').serializeObject();
                home_statistic(data);
            }
        });
    </script>
@endsection
