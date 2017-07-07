<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomePageController@homePage');
Route::get('/ho-tro/', 'HomePageController@homePage');

Auth::routes();

Route::get('/home', 'HomeController@index');
Route::get('/addon/template1', 'Customer\AddonController@get_template1');
Route::post('/cart/action', 'Customer\AddonController@executeAction');
Route::get('/san-pham-da-luu', 'Customer\ProductFavoriteController@indexs');

#region quan ly gio hang
//Route::get('/cart/add', 'Customer\AddonController@addCart');
Route::post('/cart/add', 'Customer\AddonController@addCart');

Route::get('/gio-hang', 'Customer\CartController@showCart');
Route::get('/dat-coc', 'Customer\CartController@showDeposit');
Route::get('/dat-coc-thanh-cong', 'Customer\CartController@depositSuccess');
Route::post('/cart/quantity', 'Customer\CartController@updateQuantity');
Route::post('/cart/shop/service', 'Customer\CartController@updateService');
Route::post('/cart/item/comment', 'Customer\CartController@actionUpdate');
Route::post('/gio-hang/hanh-dong', 'Customer\CartController@action');
Route::delete('/cart/item', 'Customer\CartController@deleteItem');
Route::delete('/cart/shop', 'Customer\CartController@deleteShop');
Route::post('/dat-coc', 'Customer\CartController@depositOrder');
#endregion

#region -- Mua hang --
Route::get('order_buying', 'OrderBuyingController@indexs');
Route::get('/order_buying/get_orders_data', 'OrderBuyingController@getOrdersData');
Route::post('/order_buying/set_crane_staff', 'OrderBuyingController@setCraneStaff');
#endregion

#region quan ly nhan vien
Route::get('/user', 'UserController@getUsers');
Route::get('/user/detail/{id}', 'UserController@detailUser');

Route::get('/nhan-vien/{id}', 'Customer\UserController@detail');
Route::post('/nhan-vien/dien-thoai', 'Customer\UserController@add_user_phone');
Route::put('/nhan-vien/dien-thoai', 'Customer\UserController@delete_user_phone');
Route::get('/nhan-vien/sua/{id}', 'Customer\UserController@get_user');
Route::post('/nhan-vien/sua/{id}', 'Customer\UserController@update_user');

Route::get('/user/edit/{id}', 'UserController@getUser');
Route::post('/user/edit/{id}', 'UserController@updateUser');
Route::post('/user/phone', 'UserController@addUserPhone');
Route::put('/user/phone', 'UserController@deleteUserPhone');
Route::get('/user/original_site', 'UserController@listUserOriginalSite');
Route::post('/user/original_site', 'UserController@addUserOriginalSite');
Route::put('/user/original_site/delete', 'UserController@removeUserOriginalSite');
#endregion

#region quan ly dia chi nhan hang
Route::post('/user/address', 'Customer\UserAddressController@addNewUserAddress');
Route::put('/user/address/delete', 'Customer\UserAddressController@deleteUserAddress');
Route::put('/user/address/default', 'Customer\UserAddressController@setDefaultUserAddress');
#endregion

#region -- quet ma vach --
Route::get('/scan', 'ScanController@indexs');
Route::get('/scan/statistic', 'ScanController@statistic');
Route::post('/scan/action', 'ScanController@action');
#endregion

#region -- kien hang --
Route::get('/packages', 'PackageController@indexs');
Route::get('/package', 'PackageController@index');
Route::post('/package/action', 'PackageController@action');
Route::get('/package/{code}', 'PackageController@detail');
#endregion

#region quan ly don hang
Route::get('/order', 'OrderController@orders');
Route::get('/order/get_orders_data', 'OrderController@getOrdersData');

Route::get('/order/{id}', 'OrderController@order');
Route::get('/order/detail/{id}', 'OrderController@order');
Route::post('/order/{id}/freight_bill', 'OrderController@insertFreightBill');
Route::put('/order/{id}/freight_bill', 'OrderController@removeFreightBill');
Route::post('/order/{id}/original_bill', 'OrderController@insertOriginalBill');
Route::put('/order/{id}/original_bill', 'OrderController@removeOriginalBill');
Route::post('/order/{id}/action', 'OrderController@action');

#endregion

#region comment
Route::post('/comment', 'CommentController@action');
#endregion

#region he thong
Route::get('/setting', 'SystemController@getList');
Route::post('/setting', 'SystemController@update');

Route::get('/setting/roles', 'SystemController@roles');
Route::get('/setting/role/{id}', 'SystemController@roleDetail');
Route::post('/setting/role/update/{id}', 'SystemController@updateRole');
Route::post('/setting/role', 'SystemController@addRole');
Route::post('/setting/role/permission', 'SystemController@savePermission');
Route::post('/setting/role/user', 'SystemController@updateUserRole');
Route::put('/setting/role/delete', 'SystemController@deleteRole');

//======== Warehouse ==========
Route::get('/warehouses', 'WarehouseController@render');
Route::post('/warehouse', 'WarehouseController@insert');
Route::put('/warehouse/delete', 'WarehouseController@delete');

Route::get('/warehouses_manually', 'WarehouseController@render_manually');
Route::post('/warehouses_manually', 'WarehouseController@insert_manually');
Route::put('/warehouses_manually/delete', 'WarehouseController@delete_manually');

#endregion

Route::get('hosivan', 'HoSiVanController@index');

#region -- giao dich --
Route::get('transaction/statistic', 'UserTransactionController@statisticTransaction');
Route::get('transactions', 'UserTransactionController@getTransactions');
Route::get('transaction/adjustment', 'UserTransactionController@renderTransactionAdjustment');
Route::post('transaction/adjustment', 'UserTransactionController@createTransactionAdjustment');
#endregion

#region chuc nang nhap/xuat kho cua giang
Route::get('warehouse','ExportWarehouseController@index');
Route::post('actionWarehouse', 'ExportWarehouseController@actionWarehouse');
#endregion

Route::get('/404', 'OtherController@renderPageNotFound');
Route::get('/403', 'OtherController@renderPageNotPermission');
Route::get('/vue', 'OtherController@renderExampleVue');

//================ CUSTOMER ==============
#region -- giao dich --
Route::get('giao-dich', 'Customer\UserTransactionController@getTransactions');
#endregion

#region -- don hang --
Route::get('/don-hang', 'Customer\OrderController@orders');
Route::get('/don-hang/{id}', 'Customer\OrderController@order');
Route::post('/don-hang/{id}/hanh-dong', 'Customer\OrderController@action');
#endregion

#region -- thong bao --
Route::get('/thong-bao', 'Customer\NotificationController@indexs');
#endregion

#region -- bai viet --
Route::get('/taxonomies', 'TaxonomyController@indexs');
Route::get('/taxonomy', 'TaxonomyController@createTaxonomy');

Route::get('/posts', 'PostController@indexs');
Route::get('/post', 'PostController@createPost');
Route::get('/post/{id}', 'PostController@createPost');
Route::get('/post/preview/{id}', 'PostController@previewPost');
Route::post('/post/action', 'PostController@action');

#endregion

#region -- ho tro --
Route::get('/ho-tro/danh-muc/{id}', 'Support\TaxonomyController@indexs');
Route::get('/ho-tro/{id}', 'Support\PostController@index');
#endregion

Route::get('/tinh-phi', 'PreviewFeeController@index');
Route::get('/calculator_fee', 'PreviewFeeController@calculatorFee');
Route::get('/manager_addon_link_error', 'SystemController@managerAddonLinkError');
Route::post('/set_done_link_error', 'SystemController@setDoneLinkError');

Route::get('/statistic/users', 'StatisticController@users');


#region --thông báo cho khách hàng--
Route::get('/thong-bao','Customer\CustomerNotificationController@index');
Route::get('/change-type-notification','Customer\CustomerNotificationController@changeTypeNotification'); // send ajax

// send code ajax
Route::get('/view-notification','Customer\CustomerNotificationController@changeStatus');
Route::get('/view-notification-crane','NotificationController@changeStatus');
#endregion --end thong báo cho khách hàng--
#region --danh sách khiếu nại --
Route::get('/tao-khieu-nai/{order_id}','Customer\ComplaintServiceController@index');
Route::get('/danh-sach-khieu-nai','Customer\ComplaintServiceController@listComplaint');
#endregion --danh sách khiếu nại--
#region -- router tạo khiếu nại người bán--
Route::post('/create-complaint','Customer\ComplaintServiceController@createComplaint');
#region --chi tiết khiếu nại--
Route::get('/chi-tiet-khieu-nai/{complaint_id}','Customer\ComplaintServiceController@complaintDetail');
#endregion --chi tiết khiếu nại--
#region danh sach khieu nai tren trang quan trị
Route::get('/complaint','ComplaintServiceController@index');
#region chi tiet khieu nại trên đơn
Route::get('/complaint-detail/{complaint_id}','ComplaintServiceController@complaintDetail');
#region --thông báo dành cho quản trị viên--
Route::get('/notification','NotificationController@index');
#endregion --kết thúc thông báo cho quản trị viên--



#region --send sms--
Route::get('/send-sms','SendSmsController@index');
Route::post('/send-sms-2','SendSmsController@ondex');
Route::post('/gui-tin-nhan','SendSmsController@sendSms');
#endregion

Route::get('/home/statistic', 'HomeController@homeStatistic');

Route::get('/package-weight','PackageWeightController@index');
Route::post('/save-package-weight','PackageWeightController@packageWeight');

Route::get('/send-email','SendMailerController@sendEmailToCustomer');

#region --thay thay đối trạng thái của đơn hàng và thời gian cập nh-ật-
Route::post('/change-status-order','Customer\OrderController@changeOrderStatus');

#endregion

#region --san luong van chuyen--
Route::get('/san-luong-van-chuyen','ReportController@index');
#endregion --ket thuc san luong van chuyen--

Route::get('iframe_random', 'SystemController@iframe_random');
#region --Thống kê doanh số khi click nút tìm kiếm--
Route::get('/san-luong-van-chuyen-dieu-kien','ReportController@reportCondition');
#endregion --kết thúc thống kê doanh số tìm kiếm--


#region -- chuc nang yeu cau giao hang --
Route::get('/DeliveryManage', 'DeliveryManageController@listView');
Route::get('/DeliveryManage/Create', 'DeliveryManageController@createView');
Route::post('/BillManage/Create', 'BillManageController@create');
Route::get('/BillManage/Detail/{id}', 'BillManageController@detailView');
Route::get('/BillManage', 'BillManageController@listView');
Route::post('/BillManage/UpdateFee', 'BillManageController@updateFee');
Route::get('/BillManage/Print/{id}', 'BillManageController@printBill');
#endregion


#region --xuat excel tai chinh--
Route::get('/export-excel-finance','ExportExcelFinaceController@exportExcelOrderFee');
#endregion --ket thuc xuat excel tai chinh --

#region --xuat excel cho ke toan--
Route::get('/export-excel-accounting','AccountingReportController@exportExcelAccounting');
#endregion --xuat excel cho ke toan--

#region --xuất excel tài chính khách nợ--
Route::get('/accouting_finance','AccountingFinanceControlCustomerController@index');
#endregion --kết thúc xuất excel theo từng khách--

Route::post('/remove-package','PackageController@removePackage');
Route::post('/update_package_weight','PackageController@updatePackageWeight');

Route::get('/SystemRunCheck', 'SystemRunCheckController@index');
Route::get('/SystemRunCheck/ProblemTypeHtml', 'SystemRunCheckController@problemTypeHtml');


Route::get('/PaidStaffSaleValue', 'PaidStaffSaleValueController@index');
Route::post('/PaidStaffSaleValue/Setting', 'PaidStaffSaleValueController@setting');
Route::post('/user/SetupSaleBuying', 'UserController@setupSaleValue');



# Redesign Customer template
Route::get('/home', 'OniHomeController@index');
Route::get('/gio-hang', 'OniDev\CartController@showCart');
Route::post('/gio-hang/hanh-dong', 'OniDev\CartController@action');
Route::get('/dat-coc', 'OniDev\CartController@showDeposit');
Route::post('/dat-coc', 'OniDev\CartController@depositOrder');
Route::get('/dat-coc-thanh-cong', 'OniDev\CartController@depositSuccess');

Route::get('/don-hang', 'OniDev\OrderController@orders');
Route::get('/don-hang/{id}', 'OniDev\OrderController@order');
Route::post('/don-hang/{id}/action', 'OniDev\OrderController@action');

Route::get('/san-pham-da-luu', 'OniDev\ProductFavoriteController@indexs');


Route::get('/nhan-vien/{id}', 'OniDev\UserController@detail');
Route::post('/nhan-vien/dien-thoai', 'OniDev\UserController@add_user_phone');
Route::put('/nhan-vien/dien-thoai', 'OniDev\UserController@delete_user_phone');
Route::get('/nhan-vien/sua/{id}', 'OniDev\UserController@get_user');
Route::post('/nhan-vien/sua/{id}', 'OniDev\UserController@update_user');

Route::get('giao-dich', 'OniDev\UserTransactionController@getTransactions');