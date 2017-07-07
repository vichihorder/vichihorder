<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Permission extends Model
{
    protected $table = 'permissions';

    #region -- User permission --
    const PERMISSION_USER_VIEW = 'USER_VIEW';
    const PERMISSION_USER_EDIT = 'USER_EDIT';
    const PERMISSION_USER_VIEW_LIST = 'USER_VIEW_LIST';
    const PERMISSION_DELETE_ROLE = 'DELETE_ROLE';
    const PERMISSION_CREATE_ROLE = 'CREATE_ROLE';
    const PERMISSION_VIEW_ROLE = 'VIEW_ROLE';
    const PERMISSION_VIEW_LIST_ROLE = 'VIEW_LIST_ROLE';
    const PERMISSION_USER_ADD_MOBILE = 'USER_ADD_MOBILE';
    const PERMISSION_USER_REMOVE_MOBILE = 'USER_REMOVE_MOBILE';
    const PERMISSION_VIEW_CART_CUSTOMER = 'VIEW_CART_CUSTOMER';
    #endregion

    #region -- Transaction permission --
    const PERMISSION_TRANSACTION_VIEW = 'TRANSACTION_VIEW';
    const PERMISSION_TRANSACTION_CREATE = 'TRANSACTION_CREATE';
    const PERMISSION_TRANSACTION_STATISTIC = 'TRANSACTION_STATISTIC';
    #endregion

    #region -- yeu cau giao hang --
    const PERMISSION_DELIVERY_MANAGE_LIST_VIEW = 'DELIVERY_MANAGE_LIST_VIEW';
    const PERMISSION_BILL_MANAGE_CREATE = 'BILL_MANAGE_CREATE';
    const PERMISSION_BILL_MANAGE_LIST_VIEW = 'BILL_MANAGE_LIST_VIEW';
    #endregion

    #region -- Taxonomy & post --
    const PERMISSION_MANAGER_TAXONOMY = 'MANAGER_TAXONOMY';
    const PERMISSION_MANAGER_POST = 'MANAGER_POST';
    #endregion

    #region -- Order permission --
    const PERMISSION_ORDER_LIST_VIEW = 'ORDER_LIST_VIEW';
    const PERMISSION_ORDER_VIEW = 'ORDER_VIEW';
    const PERMISSION_ORDER_ADD_FREIGHT_BILL = 'ORDER_ADD_FREIGHT_BILL';
    const PERMISSION_ORDER_REMOVE_FREIGHT_BILL = 'ORDER_REMOVE_FREIGHT_BILL';
    const PERMISSION_ORDER_ADD_ORIGINAL_BILL = 'ORDER_ADD_ORIGINAL_BILL';
    const PERMISSION_ORDER_REMOVE_ORIGINAL_BILL = 'ORDER_REMOVE_ORIGINAL_BILL';

    const PERMISSION_ORDER_CHANGE_SERVICE = 'ORDER_CHANGE_SERVICE';
    #endregion

    #region -- Mua hang --
    const PERMISSION_ORDER_BUYING_LIST_VIEW = 'ORDER_BUYING_LIST_VIEW';
    const PERMISSION_ORDER_BUYING_CAN_SET_CRANE_STAFF = 'ORDER_BUYING_CAN_SET_CRANE_STAFF';
    #endregion

    #regioin -- Package permission --
    const PERMISSION_PACKAGE_LIST_VIEW = 'PACKAGE_LIST_VIEW';
    const PERMISSION_PACKAGE_ADD = 'PACKAGE_ADD';
    #endregion

    #region -- Scan permission --
    const PERMISSION_SCAN_LIST_VIEW = 'SCAN_LIST_VIEW';
    #endregion

    #region -- System permission --
    const PERMISSION_UPDATE_SYSTEM_CONFIG = 'UPDATE_SYSTEM_CONFIG';
    const PERMISSION_MANAGER_USER_ORIGINAL_SITE = 'MANAGER_USER_ORIGINAL_SITE';
    const PERMISSION_MANAGER_WAREHOUSE = 'MANAGER_WAREHOUSE';

    const PERMISSION_MANAGER_WAREHOUSE_MANUALLY_VIEW = 'MANAGER_WAREHOUSE_MANUALLY_VIEW';
    const PERMISSION_MANAGER_WAREHOUSE_MANUALLY_INSERT = 'MANAGER_WAREHOUSE_MANUALLY_INSERT';
    const PERMISSION_MANAGER_WAREHOUSE_MANUALLY_DELETE = 'MANAGER_WAREHOUSE_MANUALLY_DELETE';
    const PERMISSION_MANAGER_ADDON_LINK_ERROR = 'MANAGER_ADDON_LINK_ERROR';
    const PERMISSION_MANAGER_TOTAL_PACKAGE_WEIGHT = 'TOTAL_PACKAGE_WEIGHT';

    const PERMISSION_CUSTOMER_WITHOUT = 'CUSTOMER_WITHOUT';
    const PERMISSION_SYSTEM_RUN_CHECK = 'SYSTEM_RUN_CHECK';

    #endregion

    const PERMISSION_PAID_STAFF_SALE_VALUE = 'PAID_STAFF_SALE_VALUE';
    const PERMISSION_MANAGER_PAID_STAFF_SALE_VALUE = 'MANAGER_PAID_STAFF_SALE_VALUE';
    const PERMISSION_SETUP_PAID_STAFF_SALE_VALUE = 'SETUP_PAID_STAFF_SALE_VALUE';

    #region -- Statistic money --
    const PERMISSION_STATISTIC_QUICK = 'STATISTIC_QUICK';
    const PERMISSION_STATISTIC_DETAIL = 'STATISTIC_DETAIL';
    #endregion

    public static $permissions = array(

        'order_permission' => [
            'label' => 'Đơn hàng',
            'permissions' => [
                self::PERMISSION_ORDER_LIST_VIEW => [
                    'label' => 'Xem trang danh sách đơn hàng',
                    'description' => '',
                ],
                self::PERMISSION_ORDER_VIEW => [
                    'label' => 'Xem trang chi tiết đơn hàng',
                    'description' => '',
                ],
                self::PERMISSION_ORDER_ADD_FREIGHT_BILL => [
                    'label' => 'Thêm mã vận đơn trong đơn hàng',
                    'description' => '',
                ],
                self::PERMISSION_ORDER_REMOVE_FREIGHT_BILL => [
                    'label' => 'Xoá mã vận đơn trong đơn',
                    'description' => '',
                ],
                self::PERMISSION_ORDER_ADD_ORIGINAL_BILL => [
                    'label' => 'Thêm mã đơn hàng gốc trong đơn',
                    'description' => '',
                ],
                self::PERMISSION_ORDER_REMOVE_ORIGINAL_BILL => [
                    'label' => 'Xoá mã đơn hàng gốc trong đơn',
                    'description' => '',
                ],
                self::PERMISSION_ORDER_CHANGE_SERVICE => [
                    'label' => 'Thêm/bỏ dịch vụ trên chi tiết đơn hàng',
                    'description' => '',
                ],
            ]
        ],

        'delivery' => [
            'label' => 'Giao hàng',
            'permissions' => [
                self::PERMISSION_DELIVERY_MANAGE_LIST_VIEW => [
                    'label' => 'Xem trang yêu cầu giao hàng',
                    'description' => '',
                ],
                self::PERMISSION_BILL_MANAGE_CREATE => [
                    'label' => 'Tạo phiếu giao',
                    'description' => '',
                ],
                self::PERMISSION_BILL_MANAGE_LIST_VIEW => [
                    'label' => 'Xem danh sách phiếu giao hàng',
                    'description' => '',
                ],
            ]
        ],

        'order_buying' => [
            'label' => 'Mua hàng',
            'permissions' => [
                self::PERMISSION_ORDER_BUYING_LIST_VIEW => [
                    'label' => 'Xem trang danh sách đơn mua hàng',
                    'description' => '',
                ],
                self::PERMISSION_ORDER_BUYING_CAN_SET_CRANE_STAFF => [
                    'label' => 'Quyền trưởng nhóm mua hàng',
                    'description' => 'Cho phép phân đơn cho nhân viên mua hàng, hủy đơn hàng',
                ],
            ]
        ],

        'package_permission' => [
            'label' => 'Kiện hàng',
            'permissions' => [
                self::PERMISSION_PACKAGE_LIST_VIEW => [
                    'label' => 'Xem trang danh sách kiện hàng',
                    'description' => '',
                ],
                self::PERMISSION_PACKAGE_ADD => [
                    'label' => 'Tạo kiện hàng',
                    'description' => '',
                ],
                self::PERMISSION_SCAN_LIST_VIEW => [
                    'label' => 'Quét mã vạch',
                    'description' => '',
                ],

            ]
        ],

        'news' => array(
            'label' => 'Bài viết',
            'permissions' => array(
                self::PERMISSION_MANAGER_TAXONOMY => array(
                    'label' => 'Quản lý danh mục bài viết',
                    'description' => '',
                ),
                self::PERMISSION_MANAGER_POST => array(
                    'label' => 'Quản lý bài viết',
                    'description' => '',
                ),
            ),
        ),

        'transaction_permission' => array(
            'label' => 'Giao dịch ',
            'permissions' => array(
                self::PERMISSION_TRANSACTION_VIEW => array(
                    'label' => 'Xem danh sách lịch sử giao dịch ',
                    'description' => '',
                ),
                self::PERMISSION_TRANSACTION_CREATE => array(
                    'label' => 'Tạo giao dịch ',
                    'description' => '',
                ),
                self::PERMISSION_TRANSACTION_STATISTIC => array(
                    'label' => 'Thống kê tài chính',
                    'description' => '',
                ),
                self::PERMISSION_MANAGER_TOTAL_PACKAGE_WEIGHT =>[
                    'label' => 'Báo cáo thông kê',
                    'description' => '',
                ]
            ),
        ),

        'user_permission' => array(
            'label' => 'Người dùng',
            'permissions' => array(
                self::PERMISSION_USER_VIEW_LIST => array(
                    'label' => 'Xem danh sách người dùng',
                    'description' => 'Quyền cho phép quản trị viên xem danh sách người dùng',
                ),
                self::PERMISSION_USER_VIEW => array(
                    'label' => 'Xem chi tiết thông tin người dùng',
                    'description' => '',
                ),
                self::PERMISSION_USER_ADD_MOBILE => array(
                    'label' => 'Thêm số điện thoại cho nhân viên',
                    'description' => '',
                ),
                self::PERMISSION_USER_REMOVE_MOBILE => array(
                    'label' => 'Xóa số điện thoại cho nhân viên',
                    'description' => '',
                ),
                self::PERMISSION_VIEW_CART_CUSTOMER => array(
                    'label' => 'Cho phép quản trị viên xem được giỏ hàng của người khác',
                    'description' => 'Lưu ý: chỉ được phép xem giỏ hàng, không được phép thao tác bất cứ hành động gì!',
                ),
                self::PERMISSION_USER_EDIT => array(
                    'label' => 'Sửa thông tin người dùng',
                    'description' => '',
                ),
            ),
        ),

        'role_permission' => array(
            'label' => 'Nhóm & phân quyền',
            'permissions' => array(
                self::PERMISSION_VIEW_LIST_ROLE => array(
                    'label' => 'Quyền xem chức năng nhóm & phân quyền',
                    'description' => '',
                ),
                self::PERMISSION_CREATE_ROLE => array(
                    'label' => 'Quyền tạo nhóm',
                    'description' => '',
                ),
                self::PERMISSION_VIEW_ROLE => array(
                    'label' => 'Quyền xem chi tiết & thao tác chỉnh sửa thông tin nhóm',
                    'description' => '',
                ),
                self::PERMISSION_DELETE_ROLE => array(
                    'label' => 'Quyền xoá nhóm',
                    'description' => '',
                )
            ),
        ),

        'system_permission' => array(
            'label' => 'Hệ thống ',
            'permissions' => array(

                self::PERMISSION_SYSTEM_RUN_CHECK => array(
                    'label' => 'Chức năng kiẻm soát vận hành',
                    'description' => 'Cho phép quản trị viên nhìn nhận thấy các vấn đề của đơn hàng để tiến hành xử lý',
                ),

                self::PERMISSION_UPDATE_SYSTEM_CONFIG => array(
                    'label' => 'Chỉnh sửa cấu hình chung trên hệ thống ',
                    'description' => 'Quyền cho phép quản trị viên chỉnh sửa cấu hình chung trên hệ thống',
                ),
                self::PERMISSION_MANAGER_USER_ORIGINAL_SITE => array(
                    'label' => 'Quản lý user mua hàng site gốc',
                    'description' => '',
                ),
                self::PERMISSION_MANAGER_WAREHOUSE => array(
                    'label' => 'Quản lý kho hàng',
                    'description' => '',
                ),

                self::PERMISSION_MANAGER_WAREHOUSE_MANUALLY_VIEW => array(
                    'label' => 'Xem trang cấu hình kho',
                    'description' => 'Cấu hình kho bằng tay, mức ưu tiên cao nhất',
                ),
                self::PERMISSION_MANAGER_WAREHOUSE_MANUALLY_INSERT => array(
                    'label' => 'Thêm cấu hình kho',
                    'description' => 'Cấu hình kho bằng tay, mức ưu tiên cao nhất',
                ),
                self::PERMISSION_MANAGER_WAREHOUSE_MANUALLY_DELETE => array(
                    'label' => 'Xóa cấu hình kho',
                    'description' => 'Cấu hình kho bằng tay, mức ưu tiên cao nhất',
                ),
                self::PERMISSION_MANAGER_ADDON_LINK_ERROR => array(
                    'label' => 'Quản lý link đặt hàng báo lỗi',
                    'description' => ''
                ),

            ),
        ),


        'statistic_permission' => array(
            'label' => 'Thống kê tài chính',
            'permissions' => array(
                self::PERMISSION_STATISTIC_QUICK => array(
                    'label' => 'Thống kê chung',
                    'description' => 'Cho phép xem thống kê trên bảng chung',
                ),
                self::PERMISSION_STATISTIC_DETAIL => array(
                    'label' => 'Thống kê tài chính theo từng khách hàng',
                    'description' => 'Cho phép xem thống kê tài chính trên từng khách hàng',
                ),
                self::PERMISSION_CUSTOMER_WITHOUT => array(
                    'label' => 'Thống kê tài chinh khách nợ',
                    'description' => 'Cho kế toán biết khách đã nợ bao nhiêu'

                ),

                self::PERMISSION_SETUP_PAID_STAFF_SALE_VALUE => array(
                    'label' => 'Quyền thiết lập lương, hoa hồng cho nhân viên mua hàng',
                    'description' => ''

                ),

                self::PERMISSION_MANAGER_PAID_STAFF_SALE_VALUE => array(
                    'label' => 'Truởng nhóm mua hàng',
                    'description' => 'Cho phép nhìn thấy toàn bộ lương của các bạn mua hàng'

                ),

                self::PERMISSION_PAID_STAFF_SALE_VALUE => array(
                    'label' => 'Xem doanh số, lương mua hàng',
                    'description' => ''

                ),

            ),
        ),


    );

    /**
     * @author vanhs
     * @desc Ham kiem tra 1 user co quyen chi dinh hay khong?
     * @param $permission_code
     * @return bool
     */
    public static function isAllow($permission_code){
        $user_id = Auth::user()->id;
        $user_section = Auth::user()->section;

        $current_user = User::find($user_id);

        if($current_user->isGod()){
            return true;
        }

        if($user_section == User::SECTION_CUSTOMER):
            return false;
        endif;

        $user_permission = Cache::get("user_permission_{$user_id}");
        if(empty($user_permission)):
            $user_permission = [];
        endif;

        if(in_array($permission_code, $user_permission)):
            return true;
        endif;

        return false;
    }
}
