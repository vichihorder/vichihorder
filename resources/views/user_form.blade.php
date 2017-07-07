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
                                                        ['name' => 'Nhân viên', 'link' => url('user')],
                                                        ['name' => $page_title, 'link' => null],
                                                    ]
                                                ]
                                            )
                <div class="card-body">



                    <div class="row">

                        <div class="col-md-4">

                            @if (count($errors) > 0)
                                <div class = "alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <?php

                                $section_metadata = ['class' => 'select2 form-control'];
                                if($user['section'] == App\User::SECTION_CRANE):
                                    $section_metadata['disabled'] = 'disabled';
                                endif;

                                echo Form::open(array('url' => url('user/edit', $user['id'] )));
                                echo Form::text('name', $user['name'], ['class' => 'form-control', 'placeholder' => 'Họ & tên', 'autofocus' => 'autofocus']);
                                echo Form::password('password', ['class' => 'form-control', 'placeholder' => 'Mật khẩu']);
                                echo Form::text('email', $user['email'], ['class' => 'form-control', 'placeholder' => 'Email', 'disabled' => 'disabled']);
                                echo Form::select('section', $section_list, $user['section'], $section_metadata);
                                echo Form::select('status', $status_list, $user['status'], ['class' => 'select2 form-control']);
                                echo Form::number('order_deposit_percent', $user['order_deposit_percent'], ['class' => 'select2 form-control']);
                                echo Form::submit('Lưu', ['class' => 'btn btn-primary']);
                                echo Form::hidden('id', $user_id);
                                echo Form::close();

                            ?>

                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

