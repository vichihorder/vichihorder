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

                    <form action="/san-luong-van-chuyen" method="GET">
                        <div class="row" class="col-sm-12">



                            <div class="col-sm-2">
                                <span>
                                    <?php
                                        $warehouse = request()->get('warehouse');
                                    ?>
                                    <div class="form-group">
                                          <select class="form-control" name="warehouse">
                                              <option value="0">Chọn kho</option>
                                              <option value="CNGZ"
                                              <?php
                                                      if ($warehouse == 'CNGZ') {
                                                          echo "selected";
                                                      }
                                                      ?>
                                              >CNGZ - Kho Quảng Châu</option>
                                              <option value="CNPX"
                                              <?php
                                                      if ($warehouse == 'CNPX') {
                                                          echo "selected";
                                                      }
                                                      ?>

                                              >CNPX - Bằng Tường</option>
                                              <option value="K-HN"
                                              <?php
                                                      if ($warehouse == 'K-HN') {
                                                          echo "selected";
                                                      }
                                                      ?>

                                              >K-HN - Hà Nội</option>
                                              <option value="S-SG"
                                              <?php
                                                      if ($warehouse == 'S-SG') {
                                                          echo "selected";
                                                      }
                                                      ?>

                                              >S-SG - Sài Gòn</option>
                                          </select>
                                        </div>
                                </span>
                            </div>

                            <div class="col-sm-2">
                                <span>
                                    <?php
                                    $warehouse_status = request()->get('warehouse_status');
                                    ?>
                                    <div class="form-group">
                                          <select class="form-control" name="warehouse_status">
                                            <option value="0">Trạng thái</option>
                                            <option value="IN"
                                            <?php
                                                    if ($warehouse_status == 'IN') {
                                                        echo "selected";
                                                    }
                                                    ?>


                                            >Nhập</option>
                                            <option value="OUT"

                                            <?php
                                                    if ($warehouse_status == 'OUT') {
                                                        echo "selected";
                                                    }
                                                    ?>

                                            >Xuất</option>
                                          </select>
                                        </div>
                                </span>
                            </div>


                            <div class="col-sm-3">
                                    <span>

                                        <?php

                                        $date_from = empty(request()->get('date1')) ? date('Y-m-d') : request()->get('date1');
                                        $date_to = empty(request()->get('date2')) ? date('Y-m-d') : request()->get('date2');

                                        ?>

                                        Từ: {{ Form::text('date1', $date_from, array('id' => 'datepicker') )}}
                                    </span>
                            </div>

                            <div class="col-sm-3">
                                <span>
                                 Đến: {{ Form::text('date2', $date_to, array('id' => 'datepicker2') )}}
                                </span>
                            </div>

                            <div>
                                <span>
                                    <button type="submit" class="btn btn-primary"

                                    >Tìm Kiếm</button>
                                </span>
                            </div>

                        </div>
                        <p>
                            <span>Tổng số kiện hàng <strong>{{ $total_package }} kiện</strong></span>
                        </p>
                        <p>
                            <span>Tổng sản lượng Cân nặng <strong>{{ $package_weight }} kg</strong></span>
                        </p>
                        <p>
                            <span>Tổng phí mua hàng ( 1% ) <strong>{{ App\Util::formatNumber($total_buying_fee) }}
                                    VND</strong></span>
                        </p>
                        <p>
                            <span>Tổng tiền vận chuyển nội địa Trung Quốc <strong>{{ App\Util::formatNumber($total_domictic_shipping_fee)}}
                                    VND</strong> </span>
                        </p>
                        <p>
                            <span>Tổng tiền hàng <strong>{{App\Util::formatNumber($customer_payment_order)}}
                                    VND</strong></span>
                        </p>


                    </form>

                    <form action="{{ url('export-excel-accounting') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control" name="username">
                                        <option value="0">Tất cả khách hàng</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <span>
                                    <button type="submit" class="btn btn-primary"

                                    >Xuất Khách Nợ</button>
                                </span>
                            </div>
                        </div>

                    </form>

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