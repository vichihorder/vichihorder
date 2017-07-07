<?php
//die(phpinfo());
?>

<!DOCTYPE html>
<html>
<head>
    <title>NhatMinh247 - @yield('page_title')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

    @section('css_top')

    @show

    @include('footer_var_view')

    @section('js_top')
    @show
</head>
<body>

@yield('content')

@yield('widget')

@section('css_bottom')
@show

@section('js_bottom')
    <script type="text/javascript" src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
@show

</body>
</html>