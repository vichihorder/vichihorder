@extends('layouts.app')

@section('page_title')
    {{$page_title}}
@endsection

@section('content')
    <div class="row">

        <form method="post"  action="{{'/save-package-weight'}}">
            <table class="table table-bordered" border="0">
                <tr>
                    <td colspan="2">{{ session('status') }}</td>
                </tr>
                <tr>
                    <td>Mã Kiện</td>
                    <td>
                        <input type="text" name="packageBarcode" >
                    </td>
                </tr>
                <tr>
                    <td>
                        Cân nặng
                    </td>
                    <td>
                        <input type="text" name="packageWeight">
                    </td>
                </tr>
                <tr>
                   <td colspan="2" align="center">
                       <button type="submit">Lưu</button>
                   </td>
                </tr>
            </table>
        </form>

    </div>

@endsection

@section('css_bottom')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-select.min.css') }}">
@endsection

@section('js_bottom')
    @parent

    <script type="text/javascript" src="{{ asset('js/jquery.lazy.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-select.min.js') }}"></script>

@endsection
