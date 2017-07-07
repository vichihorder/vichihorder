@extends('layouts.app')

@section('page_title')
    {{$page_title}}
@endsection

@section('content')

    <div class="row">
        <div class="col-xs-12">

            @if($current_user->section == App\User::SECTION_CRANE)

                <div class="row">

                    <div class="col-sm-4 col-xs-12">
                        <a class="card card-banner card-green-light">
                            <div class="card-body">
                                <i class="icon fa fa-shopping-basket fa-4x"></i>
                                <div class="content">
                                    <div class="title">Đơn đặt cọc trong ngày</div>
                                    <div class="value">{{$total_order_deposit_today}}</div>
                                </div>
                            </div>
                        </a>

                        <br>

                        <a class="card card-banner card-yellow-light">
                            <div class="card-body">
                                <i class="icon fa fa-user-plus fa-4x"></i>
                                <div class="content">
                                    <div class="title">Khách đăng ký trong ngày</div>
                                    <div class="value"><span class="sign"></span>{{$total_customer_register_today}}</div>
                                </div>
                            </div>
                        </a>



                    </div>

                    @if($permission['can_view_statistic_money_quick'])
                    <div class="col-sm-8 col-xs-12">
                        <div class="card card-mini">
                            <div class="card-header">
                                <div class="card-title text-uppercase">
                                    Thống kê tài chính
                                    <br>
                                    <div style="text-transform: none;
    font-size: 14px;">
                                        <form action="" onsubmit="return false;" class="_filter-statistic">
                                            Từ
                                            <input name="start_date" type="date" data-date="" data-date-format="DD MMMM YYYY" value="{{ sprintf('%s', date('Y-m-d'))  }}">
                                            đến
                                            <input name="end_date" type="date" data-date="" data-date-format="DD MMMM YYYY" value="{{ sprintf('%s', date('Y-m-d'))  }}">
                                        </form>
                                    <p style="color: grey; font-size: 12px;">Định dạng: tháng/ngày/năm</p>

                                    </div>
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
                            <div class="card-body no-padding table-responsive" id="_home-statistic"></div>
                        </div>



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

                    </div>
                    @endif

                </div>
                <br>


            @endif

            <div class="card">
                @include('partials/__breadcrumb',
                    [
                        'urls' => [
                            ['name' => 'Bảng chung', 'link' => null],
                        ]
                    ]
                )
                <div class="card-body">
                    <h3 class="cart-title">Hướng dẫn dành cho khách hàng mới</h3>
                    <a class="" href="{{ url('ho-tro', 4)  }}">Hướng dẫn cài đặt công cụ đặt hàng & đặt cọc đơn hàng</a><br>
                    <a class="" href="{{ url('ho-tro', 5)  }}">Hướng dẫn tìm nguồn hàng trên website taobao.com, tmall.com, 1688.com</a><br>
                    <a class="" href="{{ url('ho-tro', 1)  }}">Hướng dẫn nạp tiền vào tài khoản</a><br>
                    <a class="" href="{{ url('ho-tro', 3)  }}">Xem biểu phí</a><br>

                    <br>

                    <a href="{{ url('')  }}"><< Về trang chủ</a>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('js_bottom')
    @parent
    <script src="{{ asset('js/jquery.animateNumber.js')  }}"></script>
    <script>
        $(document).ready(function(){

            /* thong ke mua hang */




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
