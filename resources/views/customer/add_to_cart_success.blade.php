<style>
    .modal *,
    .modal *:before,
    .modal *:after {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }
    .modal {
        bottom: 0;
        display: block;
        left: 0;
        position: fixed;
        right: 0;
        top: 0;
        z-index: -1;
        font-family: arial;
    }
    .book-nhatminh247 {
        z-index: 9999999999;
        position: relative;
    }
    .book-nhatminh247.in .modal {
        z-index: 99999;
    }
    .modal .modal-dialog {
        transform: translate(0px, -200px);
        transition: transform 0.3s ease-out 0s;
        margin: 30px auto;
        width: 400px;
    }
    .book-nhatminh247.in .modal .modal-dialog {
        transform: translate(0px, 0px);
    }
    .modal-dialog {
        margin: 10px;
        position: relative;
        width: auto;
        z-index: 1050;
        background: #f0ffe5;
    }
    .modal-content {
        border: 1px solid rgba(0, 0, 0, 0.2);
        border-radius: 2px;
        outline: medium none;
        position: relative;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
    }
    .book-nhatminh247 .modal-backdrop{

    }
    .book-nhatminh247.in .modal-backdrop {
        background-color: #000000;
        bottom: 0;
        left: 0;
        position: fixed;
        right: 0;
        top: 0;
        z-index: 1030;
        opacity: 0.5;
    }
    .modal-header{
        padding: 20px 20px 10px 20px;
    }
    .modal-title {
        line-height: 1.42857;
        margin: 0;
        color: #019875;
        text-align: center;
        text-transform: uppercase;
        position: relative;
        font-weight: bold;
    }
    .modal-body {
        padding: 0 20px;
        position: relative;
    }
    .modal-body p{
        margin: 0;
        font-size: 12px;
        text-align: center;
    }
    .modal-footer {
        margin-top: 15px;
        padding: 0px 20px 15px 20px;
        display: inline-block;
        width: 100%;
        text-align: center;
    }
    .modal-footer button.next{
        border-radius: 2px;
        color: #FFFFFF;
        background: none repeat scroll 0 0 #29c75f;
        border: 1px solid #29c75f;
        padding: 6px 12px 6px 25px;
        position: relative;
    }
    .modal-footer button.next .ico{
        height: 20px;
        left: 2px;
        position: absolute;
        top: 6px;
        width: 26px;
    }
    .modal-footer button.next .ico img{
        display: inline-block;
        width: 100%;
    }
    .modal-footer button.next:hover{
        cursor: pointer;

        background: none repeat scroll 0 0 #52e083;
        border: 1px solid #52e083;

        /*border: 1px solid #c53838;*/
        /*background: -moz-linear-gradient(top,  #df3f3f 0%, #c53838 100%);*/
        /*background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#df3f3f), color-stop(100%,#c53838));*/
        /*background: -webkit-linear-gradient(top,  #df3f3f 0%,#c53838 100%);*/
        /*background: -o-linear-gradient(top,  #df3f3f 0%,#c53838 100%);*/
        /*background: -ms-linear-gradient(top,  #df3f3f 0%,#c53838 100%);*/
        /*background: linear-gradient(top,  #df3f3f 0%,#c53838 100%);*/
    }
    .modal-footer .cart{
        text-decoration: none;
        color: #0072BC;
        font-size: 12px;
        margin-left: 20px;
    }
    .modal-footer .cart:hover{
        text-decoration: underline;
    }
    .modal-dialog .close {
        color: #111;
        position: absolute;
        top: 0;
        right: 8px;
        font-size: 30px;
        font-weight: normal;
        line-height: 1;
        opacity: 0.2;
        text-shadow: 0 1px 0 #FFFFFF;
        z-index: 9999999999999999999;
    }
    .modal-dialog .close:hover,
    .modal-dialog .close:focus {
        color: #111;
        cursor: pointer;
        opacity: 0.5;
        text-decoration: none;
    }

    .modal-dialog.nhatminh247-error{
        background: #F2DEDE;
    }
    .modal-dialog.nhatminh247-error .modal-title{
        color : #B94A48;
    }
</style>

<div class="book-nhatminh247 in" id="confirm_main">
    <div class="modal-backdrop"></div>
    <div class="modal">
        <div class="modal-dialog @if(!$success) nhatminh247-error @endif">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close" onclick="document.getElementById('confirm_main').parentNode.removeChild(document.querySelectorAll('.book-nhatminh247')[document.querySelectorAll('.book-nhatminh247').length - 1])" >×</span>
                    <h5 class="modal-title">
                        <span>

                            {!! $header !!}


                        </span>
                    </h5>
                </div>
                <div class="modal-body">
                    <p class="normal">

                        {!! $body !!}

                    </p>

                    {{--@if($is_translate == 1)--}}
                        {{--<p style="text-transform: uppercase;color: red;padding: 10px;">Chúng tôi không khuyến cáo rằng không sử dụng google dịch khi tiến hành đặt hàng, điều này có thể dẫn đến việc các sản phẩm của cùng 1 shop bị tách ra làm nhiều đơn. Trân trọng</p>--}}
                    {{--@else--}}

                    {{--@endif--}}

                </div>

                <div class="modal-footer">
                    {!! $footer !!}

                </div>
            </div>
        </div>
    </div>
</div>