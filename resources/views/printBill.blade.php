<!DOCTYPE html>
<html>
<head>
    <title>In phieu #{{$bill->code}}</title>
    <style>
        /* http://meyerweb.com/eric/tools/css/reset/
   v2.0 | 20110126
   License: none (public domain)
*/

        html, body, div, span, applet, object, iframe,
        h1, h2, h3, h4, h5, h6, p, blockquote, pre,
        a, abbr, acronym, address, big, cite, code,
        del, dfn, em, img, ins, kbd, q, s, samp,
        small, strike, strong, sub, sup, tt, var,
        b, u, i, center,
        dl, dt, dd, ol, ul, li,
        fieldset, form, label, legend,
        table, caption, tbody, tfoot, thead, tr, th, td,
        article, aside, canvas, details, embed,
        figure, figcaption, footer, header, hgroup,
        menu, nav, output, ruby, section, summary,
        time, mark, audio, video {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline;
        }
        /* HTML5 display-role reset for older browsers */
        article, aside, details, figcaption, figure,
        footer, header, hgroup, menu, nav, section {
            display: block;
        }
        body {
            line-height: 1;
        }
        ol, ul {
            list-style: none;
        }
        blockquote, q {
            quotes: none;
        }
        blockquote:before, blockquote:after,
        q:before, q:after {
            content: '';
            content: none;
        }
        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        #container{
            padding: 30px;
            font-family: arial;
        }

        .page-title{
            padding: 10px;
            text-align: center;
            position: relative;
        }

        #logo{
            position: absolute;
            top: 0;
            left: 0;
        }

        #logo > h1{
            text-align: left;
            font-weight: bold;
            margin-top: 0px;
            text-transform: uppercase;
            font-size: 23px;
        }

        #logo > p{
            font-size: 10px;
            font-style: italic;
        }

        .page-title > h1{
            font-size: 30px;
            padding-top: 20px;
        }

        .date{
            font-size: 15px;
            padding-top: 10px;
            font-style: italic;
        }

        .page-content{
            padding-top: 20px;
        }

        .row{
            padding-bottom: 0px;
            border-bottom: 1px dotted #000;
            margin-bottom: 5px;
        }

        .line-bottom{
            /*border-bottom: 1px dotted #040404;*/
            line-height: 25px;
        }

        table, td, th {
            border: 1px solid #000;
            text-align: left;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 15px;
        }

        thead th{
            /*text-align: center;*/
            text-transform: uppercase;
            background: #ddd;
            /*font-size: 18px;*/
            font-weight: bold;
            padding: 8px 15px;
        }

        tbody td{
            padding: 8px 15px;
            border-bottom: 1px solid #ccc;
        }

        tbody tr:last-child td{
            border-bottom: 1px solid #000;
        }

        .three-column{
            display: inline-block;
            width: 32.33%;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div id="container">
    <div class="page-title">
        <div id="logo">
            <h1>NhatMinh247</h1>
            <p>
                GIEO CHỮ TÍN- GẶT NIỀM TIN
            </p>
        </div>


        <h1>PHIẾU GIAO HÀNG</h1>
        <div class="date">
            Ngày ... Tháng ... Năm ...
        </div>
    </div>

    <div class="page-content">
        <div class="row">
            Người nhận hàng: <span class="line-bottom">{{ $bill->buyer_address->reciver_name }}</span>
        </div>
        <div class="row">
            Điện thoại: <span class="line-bottom">{{ $bill->buyer_address->reciver_phone }}</span>
        </div>
        <div class="row">
            Địa chỉ: <span class="line-bottom">{{ $bill->buyer_address->detail }}, {{ $bill->buyer_address->district->label }}, {{ $bill->buyer_address->province->label }}</span>
        </div>

        <table id="" style="margin-top: 20px; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th WIDTH="10%" style="text-align: center;">TT</th>
                    <th width="20%">Đơn hàng</th>
                    <th width="20%">Kiện hàng</th>
                    <th width="50%">Ghi chú</th>
                </tr>
            </thead>
            <tbody>



                @foreach($bill->packages as $idx => $package)
                    <tr>
                        <td style="text-align: center;">{{$idx+1}}</td>
                        <td>{{$package->order->code}}</td>
                        <td>{{$package->logistic_package_barcode}}</td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row">
            Tiền thu hộ: <span class="line-bottom">{{ App\Util::formatNumber($bill->amount_cod)  }} đ</span>
        </div>

        <div class="row">
            Tiền giao hàng: <span class="line-bottom">{{ App\Util::formatNumber($bill->domestic_shipping_vietnam)  }} đ</span>
        </div>

        <div style="padding-top: 30px;">
            <div class="three-column">Người nhận</div>
            <div class="three-column">Thủ kho</div>
            <div class="three-column">Người giao</div>
        </div>
    </div>
</div>

<script>
    window.print();
</script>

</body>
</html>