@extends('layouts.app')

@section('page_title')
    {{$page_title}}
@endsection

<?php
if (!empty($_GET['status'])) {
    switch ($_GET['status']) {
        case 'succ':
            $statusMsgClass = 'alert-success';
            $statusMsg = 'Upload thành công';
            break;
        case 'err':
            $statusMsgClass = 'alert-danger';
            $statusMsg = ' Upload thất bại !';
            break;
        case 'invalid_file':
            $statusMsgClass = 'alert-danger';
            $statusMsg = 'Hãy up đúng định dạng CSV';
            break;
        default:
            $statusMsgClass = '';
            $statusMsg = '';
    }
}
?>

@section('content')
    <div class="container">
        <?php if (!empty($statusMsg)) {
            echo '<div style="width: 90%" class="alert ' . $statusMsgClass . '">' . $statusMsg . '</div>';
        } ?>

        <div class="panel panel-default" style="width: 90%">
            <div class="panel-heading">
                Danh sách khách hàng
                <a href="javascript:void(0);" onclick="$('#importFrm').slideToggle();">upload danh sách</a>
            </div>
            <div class="panel-body">
                <form action="{{ url('send-sms-2') }}" method="post" enctype="multipart/form-data" id="importFrm">
                    <input type="file" name="file" style="float: left"/>
                    <input type="submit" class="btn btn-primary" name="importSubmit" value="UPLOAD" style="margin-bottom: 20px;">
                </form>
                <form action="#">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>STT</th>
                        <th><input type="checkbox" id="checkall" class="check_all" /> Gửi SMS </th>
                        <th>Tên khách</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Trạng thái SMS</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($data_sms) > 0)
                        <?php $i = 1; ?>
                    @foreach($data_sms as $item_sms)
                        <tr>
                            <td>{{ $per_page * $page + $i++ }}</td>
                            <td><input type="checkbox" class="_checkboxSys"  value="{{ $item_sms->phone }}"/></td>
                            <td>{{ $item_sms->name }}</td>
                            <td>{{ $item_sms->email }}</td>
                            <td>{{ $item_sms->phone }}</td>
                            <td>{{ \App\Library\Sms\SendSmsToCustomer::getStatus($item_sms->status) }}</td>
                        </tr>
                    @endforeach
                    @else
                        <tr>
                            <td colspan="5">Chưa có dữ liệu !</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                    </form>
                {{$data_sms->links()}}
                <button type="button" class="btn btn-success _sendsms">Gửi tin</button>
            </div>
        </div>
    </div>



@endsection

@section('css_bottom')
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        .pagination{
            float: right;
            margin-right: 0px;
            }
    </style>

@endsection
@section('js_bottom')
    @parent
    <script>
        $(document).ready(function(){
            //Error happens here, $ is not defined.
            $('#importFrm').hide();

            $(".check_all").change(function () {
                $("input:checkbox").prop('checked', $(this).prop("checked"));
            });


            $("._sendsms").click(function (response) {
                var list_product_id = [];
                $('._checkboxSys:checked').each(function () {
                    list_product_id.push($(this).attr('value'));
                });
                if(list_product_id.length == 0){
                    alert('Chọn khách hàng để gửi tin');
                }
                
                $.ajax({
                    type : 'POST',
                    url : '/gui-tin-nhan',
                    data : {
                        list_phone : list_product_id
                    }
                }).done(function (response) {
                        console.info(response.status);
                });
            });


        });
    </script>
@endsection





