<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title') - NhatMinh247</title>


    <link rel="stylesheet" href="{!! asset('oniasset/css/vendor.css') !!}" />
    <link rel="stylesheet" href="{!! asset('oniasset/css/app.css') !!}" />
    <link rel="stylesheet" href="{!! asset('oniasset/css/plugins/sweetalert/sweetalert.css') !!}"/>
    <link rel="stylesheet" href="{!! asset('oniasset/css/style.css') !!}" />

    @include('footer_var_view')

    @section('header-scripts')
    @show
</head>
<body class="fixed-nav top-navigation">

  <!-- Wrapper-->
    <div id="wrapper">

        <!-- Page wraper -->
        @hasSection('sidebar')
            <div id="page-wrapper" class="gray-bg sidebar-content">
        @else
            <div id="page-wrapper" class="gray-bg">
        @endif


        <!-- Page wrapper -->
        @include('onilayouts.topnavbar')

        <!-- Sidebar panel  -->
        @section('sidebar')
        @show

        <!-- Main view  -->
            <div class="wrapper wrapper-content">
                <div class="row">
                    <div class="col-md-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Thành viên #{{  $user->code }}</h5>
                                <div class="ibox-tools">
                                    <a href="{{ url('nhan-vien/sua', $user_id) }}" class="collapse-link">
                                        <i class="fa fa-pencil-square"></i> Sửa thông tin
                                    </a>
                                </div>
                            </div>
                            <div>
                                <div class="ibox-content no-padding border-left-right">
                                    <img alt="image" class="img-responsive" src="{{ asset('images/order.jpg') }}">
                                </div>
                                <div class="ibox-content profile-content">
                                    <h4><strong>{{ $user->name }}</strong></h4>
                                    <p><i class="fa fa-check-square-o"></i> Trạng thái: {{ App\User::getStatusName($user->status) }}</p>
                                    <p><i class="fa fa-calendar"></i> Tham gia: {{ App\Util::formatDate($user->created_at)  }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9 animated fadeInRight">
                        @yield('content')
                    </div>
                </div>
            </div>

        <!-- Footer -->
        @include('onilayouts.footer')

    </div>
    <!-- End page wrapper-->

</div>
<!-- End wrapper-->

<script type="text/javascript" src="{!! asset('oniasset/js/app.js') !!}"></script>
<script type="text/javascript" src="{{ asset('js/bootbox.min.js') }}"></script>
<script type="text/javascript" src="{{asset('oniasset/js/common.js')}}"></script>
<script type="text/javascript" src="{!! asset('oniasset/js/plugins/sweetalert/sweetalert.min.js') !!}"></script>

<script type="text/javascript" src="{!! asset('oniasset/js/main.js') !!}"></script>

@section('footer-scripts')
@show

</body>
</html>
