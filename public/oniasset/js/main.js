// decode price to number
function dePrice(x){
    return x = parseFloat(x.replace(/\./g, ""));;
}
// encode number to price
function enPrice(x){
    x = parseFloat(x);
    x = x.toLocaleString(undefined, {minimumFractionDigits: 0});
    x = x.replace(/\,/g, "x");
    x = x.replace(/\./g, ",");
    x = x.replace(/\x/g, ".")
    return x;
}

// abc
function viewMore(){
    $('.more').each(function() {

        var hs = $(this).data("max-height");  // Max height display
        var moretext = 'Xem thêm <i class="fa fa-angle-double-down"></i>';
        var lesstext = 'Thu gọn <i class="fa fa-angle-double-up"></i>';
        var view_btn = '<a class="morelink _less">' + moretext + '</a></span>';
        var content = $(this).height();

        if(content > hs) {
            $(this).css("height", hs);
            $(this).addClass("collapse");
            $(this).after(view_btn);
        }

        $(".morelink").click(function(){
            if($(this).hasClass("_less")) {
                $(this).removeClass("_less");
                $(this).prev().css("height", "");
                $(this).html(lesstext);
            } else {
                $(this).addClass("_less");
                $(this).prev().css("height", hs);
                $(this).html(moretext);
            }
        });
    });
}

$(document).ready(function($) {
    viewMore();

    // Vietnamese
    jQuery.timeago.settings.strings = {
        prefixAgo: 'cách đây',
        prefixFromNow: null,
        suffixAgo: null,
        suffixFromNow: "trước",
        seconds: "chưa đến một phút",
        minute: "khoảng một phút",
        minutes: "%d phút",
        hour: "khoảng một tiếng",
        hours: "khoảng %d tiếng",
        day: "một ngày",
        days: "%d ngày",
        month: "khoảng một tháng",
        months: "%d tháng",
        year: "khoảng một năm",
        years: "%d năm",
        wordSeparator: " ",
        numbers: []
    };

    // Tooltips demo
    $('.tooltip-demo').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    });

    // Open close right sidebar
    $('.right-sidebar-toggle').on('click', function () {
        $('#right-sidebar').toggleClass('sidebar-open');
    });


    /**
     * @author Onizuka Nghia
     * Remove Cart Item
     */
    $('.__removeItem').on('click', function () {

        var $that = $(this);

        var data_send = $that.parents('.___form').serializeObject();
        var itemID = data_send.item_id;
        var shopID = data_send.shop_id;

        swal({
            title: "Bạn muốn xóa?",
            text: "Sau khi xóa Item #" + itemID + ", bạn không thể hoàn tác!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonText: "Hủy bỏ",
            confirmButtonText: "Xóa Sản phẩm",
            closeOnConfirm: false
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: data_send.url,
                    method: data_send.method,
                    data: data_send,
                    success: function (response) {
                        if (response.success) {
                            $('#shop-item-' + itemID).remove();
                            if($('#shop-'+ shopID +' .shop-item').length == 0) {
                                $('#shop-'+ shopID).remove();
                                $('.shops_count').text(response.data.statistic.total_shops);
                            }

                            $.each(response.data.shops, function(i, v) {
                                if (v.shop_id == shopID) {
                                    // Get value
                                    shop_items_count = response.data.shops[i].items.length;
                                    shop_total = response.data.shops[i].total_amount_items;
                                    shop_buying = response.data.shops[i].buying_fee;

                                    // Set new value
                                    $('#shop-'+ shopID +' .shop_items').text(shop_items_count);
                                    $('#shop-'+ shopID +' .shop_total_vnd').text(enPrice(shop_total));
                                    $('#shop-'+ shopID +' .shop_buying_fee').text(enPrice(shop_buying));

                                    // Test
                                    console.log(response.data);
                                }
                            });

                            $('.cart_qty').text(response.data.statistic.total_items);
                            $('.cart_total').text(enPrice(response.data.statistic.total_amount));

                            swal("Đã xóa thành công!", "Bạn đã xóa Item #" + itemID + " thành công.", "success");
                        }else{
                            swal({
                                title: "Thông báo",
                                text: response.message
                            })
                        }
                        $that.removeClass('disabled');
                    },
                    error: function(){
                        $that.removeClass('disabled');
                    }
                });
            }
        });
    });

    /**
     * @author Onizuka Nghia
     * Remove Cart Shop
     */
    $('.__removeShop').on('click', function () {

        var $that = $(this);

        var data_send = $that.parents('.___form').serializeObject();
        var shopID = data_send.shop_id;

        swal({
            title: "Bạn muốn xóa?",
            text: "Sau khi xóa shop #" + shopID + ", bạn không thể hoàn tác!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonText: "Hủy bỏ",
            confirmButtonText: "Xóa Shop",
            closeOnConfirm: false
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: data_send.url,
                    method: data_send.method,
                    data: data_send,
                    success: function (response) {
                        if (response.success) {
                            $('#shop-'+ shopID).remove();
                            $('.shops_count').text(response.data.statistic.total_shops);
                            $('.cart_qty').text(response.data.statistic.total_items);
                            $('.cart_total').text(enPrice(response.data.statistic.total_amount));

                            swal("Đã xóa thành công!", "Bạn đã xóa Shop #" + shopID + " thành công.", "success");
                        } else {
                            swal({
                                title: "Thông báo",
                                text: response.message
                            })
                        }
                        $that.removeClass('disabled');
                    },
                    error: function(){
                        $that.removeClass('disabled');
                    }
                });
            }
        });
    });

    /**
     * @author Onizuka Nghia
     * Change Quatity Item
     */
    $('.__changeQty').on('change', function () {

        var $that = $(this);

        var data_send = $that.parents('.___form').serializeObject();
        var itemID = data_send.item_id;
        var shopID = data_send.shop_id;

        var name = $(this).attr('name');
        var value = data_send[name];

        if(!value){
            return false;
        }

        $.ajax({
            url: data_send.url,
            method: data_send.method,
            data: data_send,
            success: function (response) {
                if (response.success) {
                    $.each(response.data.shops, function(i, v) {
                        if (v.shop_id == shopID) {
                            // Get value
                            shop_items_count = response.data.shops[i].items.length;
                            shop_total = response.data.shops[i].total_amount_items;
                            shop_buying = response.data.shops[i].buying_fee;

                            $.each(response.data.shops[i].items, function(j, y) {
                                if (y.id == itemID) {
                                    // Get value
                                    sub_total_vnd = response.data.shops[i].items[j].total_amount_item_vnd;
                                    sub_total = response.data.shops[i].items[j].total_amount_item;

                                    // Set new value
                                    $('#shop-item-' + itemID + ' .sub_total_vnd').text(enPrice(sub_total_vnd));
                                    $('#shop-item-' + itemID + ' .sub_total').text(sub_total);
                                }
                            });

                            // Set new value
                            $('#shop-'+ shopID +' .shop_items').text(shop_items_count);
                            $('#shop-'+ shopID +' .shop_total_vnd').text(enPrice(shop_total));
                            $('#shop-'+ shopID +' .shop_buying_fee').text(enPrice(shop_buying));

                            // Test
                            console.log(response.data);
                        }
                    });
                    $('.cart_qty').text(response.data.statistic.total_items);
                    $('.cart_total').text(enPrice(response.data.statistic.total_amount));
                } else {
                    swal({
                        title: "Thông báo",
                        text: response.message
                    })
                }
                $that.removeClass('disabled');
            },
            error: function(){
                $that.removeClass('disabled');
            }
        });
    });

    /**
     * @author Onizuka Nghia
     * Cancel Order
     */
    $('.__cancelOrder').on('click', function () {

        var $that = $(this);

        var data_send = $that.parents('.___form').serializeObject();

        swal({
            title: "Hủy đơn hàng?",
            text: "Sau khi hủy đơn hàng này, bạn không thể hoàn tác!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonText: "Hủy bỏ",
            confirmButtonText: "Hủy đơn hàng",
            closeOnConfirm: false
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: data_send.url,
                    method: data_send.method,
                    data: data_send,
                    success: function (response) {
                        if (response.success) {
                            $that.parents('.___form').remove();
                            $('#order_status').text('Đã hủy');
                            swal("Hủy thành công!", "Bạn đã xóa hủy đơn hàng thành công.", "success");
                        } else {
                            swal({
                                title: "Thông báo",
                                text: response.message
                            })
                        }
                        $that.removeClass('disabled');
                    },
                    error: function(){
                        $that.removeClass('disabled');
                    }
                });
            }
        });
    });
});
