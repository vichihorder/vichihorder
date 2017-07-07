@extends($layout)

@section('page_title')
    {{$page_title}}
@endsection

@section('content')
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Sửa thông tin</h5>
                    </div>
                    <div>
                        <div class="ibox-content">
                            <?php
                            $section_metadata = ['class' => 'select2 form-control'];
                            if($user['section'] == App\User::SECTION_CRANE):
                                $section_metadata['disabled'] = 'disabled';
                            endif;

                            echo Form::open(array('url' => url( 'user/edit/' . $user_id ), 'class' => 'form-horizontal'));?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Họ & tên: </label>
                                <div class="col-sm-9">
                                    {{ Form::text('name', $user['name'], ['class' => 'form-control', 'placeholder' => 'Họ & tên']) }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Mật khẩu: </label>
                                <div class="col-sm-9">
                                    {{ Form::password('password', ['class' => 'form-control', 'placeholder' => 'Mật khẩu']) }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3"></label>
                                <div class="col-sm-9">
                                    {{ Form::submit('Cập nhật', ['class' => 'btn btn-primary']) }}
                                    {{ Form::reset('Hủy bỏ', ['class' => 'btn btn-default']) }}
                                </div>
                           </div>
                            {{ Form::hidden('id', $user_id) }}
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
@endsection

