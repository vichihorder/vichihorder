@extends('layouts.app')

@section('page_title')
    {{$page_title}}
@endsection
@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="card">

                @include('partials/__breadcrumb',
                                [
                                    'urls' => [
                                        ['name' => 'Trang chủ', 'link' => url('home')],
                                        ['name' => 'Thống kê', 'link' => null],
                                    ]
                                ]
                            )

                <div class="card-body">

                   nguyen hoang giang


                </div>
            </div>
        </div>
    </div>


@endsection

@section('js_bottom')
    @parent
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <script>
        $(function () {
            $("#datepicker").datepicker({dateFormat: 'yy-mm-dd'});
            $("#datepicker2").datepicker({dateFormat: 'yy-mm-dd'});
        });
    </script>

@endsection

@section('css_bottom')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
@endsection