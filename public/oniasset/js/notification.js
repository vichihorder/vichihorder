    var xhr;

    var page = 2;
    function content_notification(view){
        $.ajax({
            url: "/load-content-notify",
            type: 'GET',
            data: {},
            dataType: 'json',
            success: function (response) {

                if (response.type == 'success') {
                    // count notifycation
                    if(response.count_notify != 0){
                        $("._count_notification").html(response.count_notify);
                    }else{
                        $("._count_notification").hide();
                    }

                    if(view == true){

                        $("._system_notify").html(response.notification);
                        if(response.notification_display > 10){

                            $("._system_notify").append('<div class="view_more_btn _load_more">Click để xem thêm</div>');

                        }
                    }
                }
            }
        });
    }
    content_notification(true);

    $(document).on('click', '.dropdown-menu.dropdown-notify', function (e) {
        e.stopPropagation();
    });

    /**
     * sự kiện khi click vào load more
     */
    $(document).on("click","._load_more",function () {
        $.ajax(
            {
                url: "/load-content-notify",
                type: "get",
                data :{
                    currentPage : page

                }
            })
            .done(function(data)
            {
                if(data.html == " "){
                    return;
                }
                $("._load_more").remove();
                $("._system_notify").append(data.notification);

                page++;

                $("._system_notify").append('<div class="view_more_btn _load_more">Click để xem thêm</div>');

                // nếu đã load hết thì xóa đi
                if($("._change_status").length == data.notification_display ){
                    $("._load_more").remove();
                }
            })


    });

    /**
     * đổi trạng thái đơn sang đã đọc
     */
    $(document).on("click","._change_status",function() {
        var follower_id = $(this).data('follower-id');
        $.ajax({
            url: "/change-status-follower",
            type: 'GET',
            data: {
                follower_id : follower_id
            },
            dataType: 'json',
            success: function (response) {
                if (response.type == 'success') {
                    // do nothing
                }
            }
        });
    });

    $(document).on("click","._mark_read_all",function () {
        $.ajax({
            url: "/mark-read-all",
            type: 'POST',
            data: {

            },
            dataType: 'json',
            success: function (response) {
                if (response.type == 'success') {
                    $("._count_notification").hide();
                    $("._change_status").each(function () {
                        $(this).removeClass('new');
                    });
                }
            }
        });
    });


    $('.scroll_content').slimscroll({
        height: '400px'
    }).bind('slimscrolling', function(e, pos){
        console.log(pos);
    });