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
                            ['name' => $page_title, 'link' => null],
                        ]
                    ]
                )
                <div class="card-body">
                    <h3>{{$page_title}}</h3>

                    <br><br>
                    @foreach($problem_list as $idx => $problem_list_item)
                        <div class="_problem-row">
                            <h4><strong>{{$idx+1}}</strong>, {!! $problem_list_item['label'] !!} <small><a

                                            style="padding-left: 10px;"
                                            class="_open"
                                            href="javascript:void(0)">Mở rộng</a> </small></h4>
                            <div class="_content hidden"></div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

@endsection

@section('js_bottom')
    @parent
    <script src="{{ asset('js/jquery.timeago.js')  }}"></script>
    <script>
        $(document).ready(function(){
            $('._problem-row').each(function(){
                get_date_from_problem_type($(this));
            });

            function get_date_from_problem_type($this){
                var problem_type = $this.find('._day_long_time').data('problem-type');
                var long_time = $this.find('._day_long_time').val();
                var paid_staff_id = $this.find('._paid_staff_id').val();

                request("{{ url('SystemRunCheck/ProblemTypeHtml')  }}", "get", {
                    problem_type:problem_type,
                    long_time:long_time,
                    paid_staff_id:paid_staff_id
                }).done(function(response){
                    if(response.success){
                        $this.find('._content').html(response.html);
                        $this.find('._content ._time-ago').timeago();
                    }else{
                        bootbox.alert(response.message);
                    }
                });
            }

            $(document).on('change', '._day_long_time', function(){
                get_date_from_problem_type($(this).parents('._problem-row'));
            });

            $(document).on('change', '._paid_staff_id', function(){
                get_date_from_problem_type($(this).parents('._problem-row'));
            });

            $(document).on('click', '._open', function(){
                 var text = $(this).text();
                 var $parent = $(this).parents('._problem-row');
                 if(text == 'Mở rộng'){
                     $parent.find('._content').removeClass('hidden');
                     $(this).text('Thu gọn');
                 }else{
                     $parent.find('._content').addClass('hidden');
                     $(this).text('Mở rộng');
                 }
            });
        });
    </script>
@endsection

