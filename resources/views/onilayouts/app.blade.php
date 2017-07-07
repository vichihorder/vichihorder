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
        @yield('content')

        <!-- Footer -->
        @include('onilayouts.footer')

    </div>
    <!-- End page wrapper-->

</div>
<!-- End wrapper-->

<script type="text/javascript" src="{!! asset('oniasset/js/app.js') !!}"></script>
<script type="text/javascript" src="{{ asset('js/bootbox.min.js') }}"></script>
<script type="text/javascript" src="{{asset('oniasset/js/common.js')}}"></script>

<script type="text/javascript" src="{!! asset('oniasset/js/plugins/timeago/jquery.timeago.js') !!}"></script>
<script type="text/javascript" src="{!! asset('oniasset/js/plugins/sweetalert/sweetalert.min.js') !!}"></script>

<script type="text/javascript" src="{!! asset('oniasset/js/main.js') !!}"></script>

@section('footer-scripts')
    <script type="text/javascript">
        $(document).ready(function($) {
            $(".timeago").timeago();
        });
    </script>
@show

</body>
</html>
