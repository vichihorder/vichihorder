<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title') - NhatMinh247</title>

    <!-- Base Css Files -->
    <link href="{!! asset('oniasset/libs/jqueryui/ui-lightness/jquery-ui-1.10.4.custom.min.css') !!}" rel="stylesheet" />
    <link href="{!! asset('oniasset/libs/bootstrap/css/bootstrap.min.css') !!}" rel="stylesheet" />
    <link href="{!! asset('oniasset/libs/font-awesome/css/font-awesome.min.css') !!}" rel="stylesheet" />
    <link href="{!! asset('oniasset/libs/fontello/css/fontello.css') !!}" rel="stylesheet" />
    <link href="{!! asset('oniasset/libs/animate-css/animate.min.css') !!}" rel="stylesheet" />
    <link href="{!! asset('oniasset/libs/nifty-modal/css/component.css') !!}" rel="stylesheet" />
    <link href="{!! asset('oniasset/libs/magnific-popup/magnific-popup.css') !!}" rel="stylesheet" />
    <link href="{!! asset('oniasset/libs/ios7-switch/ios7-switch.css') !!}" rel="stylesheet" />
    <link href="{!! asset('oniasset/libs/pace/pace.css') !!}" rel="stylesheet" />
    <link href="{!! asset('oniasset/libs/sortable/sortable-theme-bootstrap.css') !!}" rel="stylesheet" />
    <link href="{!! asset('oniasset/libs/bootstrap-datepicker/css/datepicker.css') !!}" rel="stylesheet" />
    <link href="{!! asset('oniasset/libs/jquery-icheck/skins/all.css') !!}" rel="stylesheet" />

    <link href="{!! asset('oniasset/css/style.css') !!}" rel="stylesheet" type="text/css" />
    <!-- Extra CSS Libraries End -->
    <link href="{!! asset('oniasset/css/style-responsive.css') !!}" rel="stylesheet" />
    <link href="{!! asset('oniasset/css/custom.css') !!}" rel="stylesheet" />

    @include('footer_var_view')

    @section('header-scripts')
    @show
</head>
<body class="fixed-left">

<!-- Wrapper-->
<div id="wrapper">
@include('onilayouts.topnavbar')
@include('onilayouts.navigation')
    <!-- Start right content -->
    <div class="content-page">
        <!-- ============================================================== -->
        <!-- Start Content here -->
        <!-- ============================================================== -->
        <div class="content">
        @yield('content')

        @include('onilayouts.footer')
        </div>
    </div>
    <!-- End page wrapper-->
</div>
<!-- End wrapper-->

<script>
    var resizefunc = [];
</script>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="{!! asset('oniasset/libs/jquery/jquery-1.11.1.min.js') !!}"></script>
<script src="{!! asset('oniasset/libs/bootstrap/js/bootstrap.min.js') !!}"></script>
<script src="{!! asset('oniasset/libs/jqueryui/jquery-ui-1.10.4.custom.min.js') !!}"></script>
<script src="{!! asset('oniasset/libs/jquery-ui-touch/jquery.ui.touch-punch.min.js') !!}"></script>
<script src="{!! asset('oniasset/libs/jquery-detectmobile/detect.js') !!}"></script>
<script src="{!! asset('oniasset/libs/jquery-animate-numbers/jquery.animateNumbers.js') !!}"></script>
<script src="{!! asset('oniasset/libs/ios7-switch/ios7.switch.js') !!}"></script>
<script src="{!! asset('oniasset/libs/fastclick/fastclick.js') !!}"></script>
<script src="{!! asset('oniasset/libs/jquery-blockui/jquery.blockUI.js') !!}"></script>
<script src="{!! asset('oniasset/libs/bootstrap-bootbox/bootbox.min.js') !!}"></script>
<script src="{!! asset('oniasset/libs/jquery-slimscroll/jquery.slimscroll.js') !!}"></script>
<script src="{!! asset('oniasset/libs/jquery-sparkline/jquery-sparkline.js') !!}"></script>
<script src="{!! asset('oniasset/libs/nifty-modal/js/classie.js') !!}"></script>
<script src="{!! asset('oniasset/libs/nifty-modal/js/modalEffects.js') !!}"></script>
<script src="{!! asset('oniasset/libs/sortable/sortable.min.js') !!}"></script>
<script src="{!! asset('oniasset/libs/bootstrap-fileinput/bootstrap.file-input.js') !!}"></script>
<script src="{!! asset('oniasset/libs/bootstrap-select/bootstrap-select.min.js') !!}"></script>
<script src="{!! asset('oniasset/libs/bootstrap-select2/select2.min.js') !!}"></script>
<script src="{!! asset('oniasset/libs/magnific-popup/jquery.magnific-popup.min.js') !!}"></script>
<script src="{!! asset('oniasset/libs/pace/pace.min.js') !!}"></script>
<script src="{!! asset('oniasset/libs/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>
<script src="{!! asset('oniasset/libs/jquery-icheck/icheck.min.js') !!}"></script>

<script src="{!! asset('oniasset/js/init.js') !!}" type="text/javascript"></script>

<script src="{!! asset('oniasset/js/common.js') !!}"></script>
<script src="{!! asset('oniasset/js/main.js') !!}"></script>

@section('footer-scripts')
@show

</body>
</html>
