<?php

namespace App;

use App\Library\ServiceFee\ServiceFactoryMethod;
use App\Library\ServiceFee\WoodCrating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use App\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Exception;

class Order extends Model
{
    protected $table = 'order';

    protected $factoryMethodInstance = null;
    protected $order_buying_number_fee = 3;

    const STATUS_DEPOSITED = 'DEPOSITED';
    const STATUS_BOUGHT = 'BOUGHT';
    const STATUS_SELLER_DELIVERY = 'SELLER_DELIVERY';
    const STATUS_RECEIVED_FROM_SELLER = 'RECEIVED_FROM_SELLER';
    const STATUS_TRANSPORTING = 'TRANSPORTING';
    const STATUS_WAITING_DELIVERY = 'WAITING_DELIVERY';
    const STATUS_DELIVERING = 'DELIVERING';
    const STATUS_RECEIVED = 'RECEIVED';
    const STATUS_CANCELLED = 'CANCELLED';

    public function __construct(array $attributes = [])
    {
        $this->factoryMethodInstance = new ServiceFactoryMethod();
        parent::__construct($attributes);
    }

    #region -- begin variable static --

    public static $statusTitle = array(
        self::STATUS_DEPOSITED => 'Đã đặt cọc',
        self::STATUS_BOUGHT => 'Đã mua hàng',
        self::STATUS_SELLER_DELIVERY => "Người bán giao",
        self::STATUS_RECEIVED_FROM_SELLER => "NhatMinh247 Nhận",
        self::STATUS_TRANSPORTING => "Vận chuyển",
        self::STATUS_WAITING_DELIVERY => "Chờ giao hàng",
        self::STATUS_DELIVERING => "Đang giao hàng",
        self::STATUS_RECEIVED => 'Đã nhận hàng',
        self::STATUS_CANCELLED => "Đã hủy",
    );

    public static $fieldTime = [
        self::STATUS_DEPOSITED => 'deposited_at',
        self::STATUS_BOUGHT => 'bought_at',
        self::STATUS_SELLER_DELIVERY => "seller_delivery_at",
        self::STATUS_RECEIVED_FROM_SELLER => "received_from_seller_at",
        self::STATUS_TRANSPORTING => "transporting_at",

        self::STATUS_WAITING_DELIVERY => 'waiting_delivery_at',
        self::STATUS_DELIVERING => 'delivering_at',

        self::STATUS_RECEIVED => 'received_at',
        self::STATUS_CANCELLED => 'cancelled_at',
    ];

    public static $timeListOrderDetail = [
        'deposited_at' => 'Đặt cọc',
        'bought_at' => 'Đã mua',
        'seller_delivery_at' => 'Người bán giao',
        'received_from_seller_at' => 'NhatMinh247 Nhận',
        'transporting_at' => 'Vận chuyển',
        'waiting_delivery_at' => 'Chờ giao hàng',
        'delivering_at' => 'Đang giao hàng',
        'received_at' => 'Đã nhận hàng',
        'cancelled_at' => 'Hủy đơn'
    ];

    public static $statusLevel = array(
        self::STATUS_DEPOSITED,
        self::STATUS_BOUGHT,
        self::STATUS_SELLER_DELIVERY,
        self::STATUS_RECEIVED_FROM_SELLER,
        self::STATUS_TRANSPORTING,
        self::STATUS_WAITING_DELIVERY,
        self::STATUS_DELIVERING,
        self::STATUS_RECEIVED,
        self::STATUS_CANCELLED,
    );

    public static $_endingStatus = [
        self::STATUS_RECEIVED,
        self::STATUS_CANCELLED,
    ];

    public static function getListStatusFromStatusToStatus($from_status = null, $to_status = null){
        if(!$from_status){
            $from_status = 0;
        }else{
            $from_status = array_search($from_status, self::$statusLevel);
        }
        if(!$to_status){
            $to_status = count(self::$statusLevel);
        }else{
            $to_status = array_search($to_status, self::$statusLevel);
        }
        return array_slice(self::$statusLevel, $from_status, $to_status);
    }

    #endregion

    #region -- begin function static --

    /**
     * @author vanhs
     * @desc Lay tong so don dat coc theo ngay
     * @param $day
     * @return int
     */
    public static function getTotalDepositByDay($day){//Y-m-d
        $total = self::select('id')->where([
            [ 'deposited_at', '>=', $day . ' 00:00:00' ],
            [ 'deposited_at', '<=', $day . ' 23:59:59' ],
        ])->count();
        return $total;
    }

    public static function retrieveByCode($code){
        if(empty($code)) return null;

        return self::where([
            'code' => $code
        ])->first();
    }

    /**
     * @author vanhs
     * @desc Lay danh sach ma hoa don goc + link chi tiet don hang TQ
     * @param $site
     * @param null $original_bill
     * @return array
     */
    public static function originalBillWithLink($site = null, $original_bill = null)
    {
        if (empty($site) || empty($original_bill)) {
            return null;
        }

        $href = null;
        switch (strtolower($site)) {
            case User::SITE_TAOBAO:
                $href = sprintf("http://trade.taobao.com/trade/detail/trade_item_detail.htm?bizOrderId=%s", $original_bill);
                break;
            case User::SITE_TMALL;
                $href = sprintf("http://trade.tmall.com/detail/orderDetail.htm?bizOrderId=%s", $original_bill);
                break;
            case User::SITE_1688;
                $href = sprintf("http://trade.1688.com/order/unify_buyer_detail.htm?orderId=%s", $original_bill);
                break;
        }

        return $href;
    }

    public static function getFieldTimeByStatus($field_time){
        return self::$fieldTime[$field_time] ? self::$fieldTime[$field_time] : null;
    }

    /**
     * @author vanhs
     * @desc Lay danh sach id don hang da huy (VD: 1,2,3,10,44)
     * @return null
     */
    public static function getOrderIdCancelled(){
        $query = DB::table('order')
            ->select(DB::raw('GROUP_CONCAT(id) as id'))
            ->where([
                ['status', '=', self::STATUS_CANCELLED],
                ['user_id', '!=', User::USER_ID_TEST]
            ])
            ->first();
        if($query){
            return $query->id;
        }
        return null;
    }

    /**
     * get Right Status
     * @param $status
     * @return array Status
     */
    public static function getAfterStatus($status){
        if($status == ''){
            return array();
        }

        $status_array = array();

        $key = array_search($status,Order::$statusLevel);

        for ($i = $key+1 ; $i < count(Order::$statusLevel) ;$i++) {
            $status_array[] = Order::$statusLevel[$i];
        }

        return $status_array;
    }

    public static function getFavicon($site){
        if(empty($site)) return null;
        $site = strtolower($site);
        return asset('images/favicon_site_china/' . $site . '.png');
    }

    public static function getStatusTitle($code){
        if(empty($code)){
            return null;
        }

        return empty(self::$statusTitle[$code]) ? '' : self::$statusTitle[$code];
    }

    /**
     * get Left Status
     * @param $status
     * @return array Status
     */
    public static function getBeforeStatus($status){
        if($status == ''){
            return array();
        }

        $status_array = array();

        $key = array_search($status, Order::$statusLevel);

        for ($i = $key-1 ; $i >= 0 ;$i--) {
            $status_array[] = Order::$statusLevel[$i];
        }

        return $status_array;
    }

    public static function findOneByIdOrCode($input){
        $order = self::find($input);

        if(!$order):
            $order = Order::where(['code' => $input])->first();
            if(!$order):
                return false;
            endif;
        endif;

        return $order;
    }

    public static function findByTransactions($order_id){
        return UserTransaction::where([
            'object_id' => $order_id,
            'object_type' => UserTransaction::OBJECT_TYPE_ORDER,
            'state' => UserTransaction::STATE_COMPLETED
        ])->orderBy('created_at', 'desc')
            ->get();
    }

    public static function findByOrderItemComments($order_id){
        return Comment::where([
            'parent_object_id' => $order_id,
            'parent_object_type' => Comment::TYPE_OBJECT_ORDER,
            'object_type' => Comment::TYPE_OBJECT_ORDER_ITEM,
            'scope' => Comment::TYPE_NONE,
        ])->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * @author vanhs
     * @desc Tao ma don hang
     * @param $user
     * @return string
     * @throws \Exception
     */
    public static function createCode($user)
    {
        if(!$user):
            throw new \Exception('User not found!');
        endif;
        $user_code = $user->code;

        //fail safe: if user deposit without user code, create one
        if (!$user_code) {
            $user_code = User::genCustomerCode();
            User::where(['id' => $user->id])->update([
                'updated_at' => date('Y-m-d H:i:s'),
                'code' => $user_code
            ]);
        }

        //remove shipping address province's code
        $current_order_no = self::select('id')->where([

            [ 'user_id', '=', $user->id ],
            [ 'created_at', '>=', date('Y-m-d') . ' 00:00:00' ],
            [ 'created_at', '<=', date('Y-m-d') . ' 23:59:59' ]

        ])->count();

        $serial_part = str_pad($current_order_no + 1, 1, '0', STR_PAD_LEFT);
        $time_part = date('d');

        $working_month_sequence = Util::getWorkingMonthSequence();

        return "{$user_code}_{$working_month_sequence}{$time_part}{$serial_part}";
    }

    #endregion

    /**
     * Is before status
     * @param $status
     * @param bool $includedCurrentStatus
     * @return bool
     */
    public function isBeforeStatus($status, $includedCurrentStatus = false)
    {
        if ($includedCurrentStatus && $this->status == $status) {
            return true;
        }

        $before_status = Order::getBeforeStatus($status);
        if (empty($before_status)) {
            return false;
        }
        if (in_array($this->status, $before_status)) {
            return true;
        }
        return false;
    }

    /**
     * Is After Status
     * @param $status
     * @param bool $includedCurrentStatus
     * @return bool
     */
    public function isAfterStatus($status, $includedCurrentStatus = false)
    {
        if ($includedCurrentStatus && $this->status == $status) {
            return true;
        }
        $after_status = Order::getAfterStatus($status);

        if (empty($after_status)) {
            return false;
        }
        if (in_array($this->status, $after_status)) {
            return true;
        }
        return false;
    }

    /**
     * @ Ham kiem tra xem don hang co phai la trang thai cuoi cung hay chua?
     * @return bool
     */
    public function isEndingStatus(){
        if( in_array($this->status, self::$_endingStatus) ){
            return true;
        }
        return false;
    }

    public function changeStatus($status, $save = true){

        $this->status = $status;

        $field_time = self::getFieldTimeByStatus($status);
        if($field_time){
            $this->$field_time = date('Y-m-d H:i:s');
        }

        if($save){
            $this->save();
        }

    }

    /**
     * @desc Lay danh sach san pham nam trong don hang
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getItemInOrder()
    {
        return OrderItem::where([
            'order_id' => $this->id
        ])->get();
    }

    /**
     * @desc Lay ra tong so san pham loai thuong
     * @return int
     */
    public function getItemNormalQuantity()
    {
        $total = 0;
        $items = $this->getItemInOrder();

        if (!empty($items)) {
            foreach ($items as $item) {
                if (!$item->checkItemAssess($this->exchange)) {
                    $total += $item->check_quantity;
                }
            }
        }

        return $total;
    }

    /**
     * @desc Lay ra tong so san pham la phu kien
     * @return int
     */
    public function getItemAssessQuantity()
    {
        $total = 0;
        $items = $this->getItemInOrder();

        if (!empty($items)) {
            foreach ($items as $item) {
                if ($item->checkItemAssess($this->exchange)) {
                    $total += $item->check_quantity;
                }
            }
        }

        return $total;
    }

    public function has_original_bill($original_bill){
        return OrderOriginalBill::where([
            'original_bill' => $original_bill,
            'order_id' => $this->id,
            'is_deleted' => 0
        ])->count();
    }

    public function has_freight_bill($freight_bill){
        return OrderFreightBill::where([
            'order_id' => $this->id,
            'freight_bill' => $freight_bill,
            'is_deleted' => 0
        ])->count();
    }

    public function exist_freight_bill(){
        return OrderFreightBill::where([
            'order_id' => $this->id,
            'is_deleted' => 0
        ])->count() > 0;
    }

    public function exist_original_bill(){
        return OrderOriginalBill::where([
                'order_id' => $this->id,
                'is_deleted' => 0
            ])->count() > 0;
    }

    public function create_freight_bill($user_id, $freight_bill){
        $order_freight_bill = new OrderFreightBill();
        $order_freight_bill->user_id = $user_id;
        $order_freight_bill->order_id = $this->id;
        $order_freight_bill->freight_bill = $freight_bill;
        return $order_freight_bill->save();
    }

    public function create_original_bill($user_id, $original_bill){
        $order_original_bill = new OrderOriginalBill();
        $order_original_bill->user_id = $user_id;
        $order_original_bill->order_id = $this->id;
        $order_original_bill->original_bill = $original_bill;
        return $order_original_bill->save();
    }

    public function getBuyingFee($total_amount_vnd){
        $service = $this->factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_BUYING,
            'total_amount' => $total_amount_vnd,
            'apply_time' => $this->deposited_at
        ]);
        return $service->calculatorFee();
    }

    public function getCheckingFee(){
        $total_quantity_items_normal = $this->getItemNormalQuantity();
        $total_quantity_items_assess = $this->getItemAssessQuantity();

        $service = $this->factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_CHECKING,
            'total_quantity_items_normal' => $total_quantity_items_normal,
            'total_quantity_items_assess' => $total_quantity_items_assess,
            'apply_time' => $this->deposited_at
        ]);
        return $service->calculatorFee();
    }

    public function getShippingChinaVietnam($weight){
        //-- Can nang truyen vao phai chuyen sang kg
        $service = $this->factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_SHIPPING_CHINA_VIETNAM,
            'weight' => $weight,
            'destination_warehouse' => $this->destination_warehouse,
            'apply_time' => $this->deposited_at
        ]);
        return $service->calculatorFee();
    }

    public function getWoodCrating($shipping_china_vietnam_fee, $weight_manual, $weight_equivalent, $calculator_by = null){
        $data = [
            'service_code' => Service::TYPE_WOOD_CRATING,
            'fee_shipping_china_vietnam' => $shipping_china_vietnam_fee,//phi van chuyen quoc te
            'weight_manual' => $weight_manual,//can nang tinh
            'weight_equivalent' => $weight_equivalent,//can nang quy doi
            'apply_time' => $this->deposited_at
        ];

        if($calculator_by){
            $data['calculator_by'] = $calculator_by;
        }

        $service = $this->factoryMethodInstance->makeService($data);
        return $service->calculatorFee();
    }

    /**
     * @author vanhs
     * @desc Hien thi cac phi tren don
     * @return array
     */
    public function fee(){
        $data_return = [];
        foreach(OrderFee::$fee_field_order_detail as $key => $value){
            $data_return[$key] = 0;
        }

        $fee = OrderFee::getListFee($this);
        foreach($fee as $f){
            if(!$f instanceof OrderFee){
                continue;
            }
            $fee_name = $f->name;

            $fee_money = $f->money;
            if(isset($data_return[$fee_name])){
                $data_return[$fee_name] = $fee_money;
            }
        }

        #region -- Phí đơn hàng --
        $data_return['TOTAL_FEE_VND'] =
            $data_return['AMOUNT_VND']
            + $data_return['BUYING_FEE_VND']
            + $data_return['DOMESTIC_SHIPPING_FEE_VND']
            + $data_return['SHIPPING_CHINA_VIETNAM_FEE_VND']
            + $data_return['WOOD_CRATING_VND'];
        #endregion

        #region -- Tổng thanh toán --
        if($data_return['TOTAL_FEE_VND'] > $data_return['CUSTOMER_PAYMENT_AMOUNT_VND']){
            $data_return['NEED_PAYMENT_AMOUNT_VND'] = $data_return['TOTAL_FEE_VND']
                - $data_return['CUSTOMER_PAYMENT_AMOUNT_VND'];
        }else{
            $data_return['NEED_PAYMENT_AMOUNT_VND'] = 0;
        }
        #endregion

        return $data_return;
    }

    /**
     * @author vanhs
     * @desc Ham lay ra tong phi cua don hang = tien hang + phi mua hang + vc noi dia TQ + vc quoc te + phi dong go (neu co)
     * @return int
     */
    public function getFeeAll(){
        $fee = 0;
        $query = DB::table('order_fee')
            ->select(DB::raw("sum(money) as money"))
            ->where([
                ['order_id', '=', $this->id],
            ])
            ->whereIn('name', [
                'AMOUNT_VND',
                'BUYING_FEE_VND',
                'DOMESTIC_SHIPPING_FEE_VND',
                'SHIPPING_CHINA_VIETNAM_FEE_VND',
                'WOOD_CRATING_VND'
            ])
            ->first();
        if($query){
            return $query->money;
        }
        return $fee;
    }

    public function original_bill(){
        return $this->hasMany('App\OrderOriginalBill', 'order_id');
    }

    public function freight_bill(){
        return $this->hasMany('App\OrderFreightBill', 'order_id');
    }

    public function item(){
        return $this->hasMany('App\OrderItem', 'order_id');
    }

    public function service(){
        return $this->hasMany('App\OrderService', 'order_id');
    }

    public function package(){
        return $this->hasMany('App\Package', 'order_id');
    }

    public function total_order_quantity(){
        return DB::table('order_item')
            ->select(DB::raw('SUM(order_quantity) as total_quantity'))
            ->where([
                'order_id' => $this->id
            ])
            ->first()->total_quantity;
    }

    public function total_checking_quantity(){
        return DB::table('order_item')
            ->select(DB::raw('SUM(checking_quantity) as total_quantity'))
            ->where([
                'order_id' => $this->id
            ])
            ->first()->total_quantity;
    }

    public function total_receiver_quantity(){
        return DB::table('order_item')
            ->select(DB::raw('SUM(receiver_quantity) as total_quantity'))
            ->where([
                'order_id' => $this->id
            ])
            ->first()->total_quantity;
    }

    /**
     * @author vanhs
     * @desc Kiem tra don hang co ton tai dich vu hay khong?
     * @param $service_code
     * @return mixed
     */
    public function existService($service_code){
        $where['service_code'] = $service_code;
        $where['order_id'] = $this->id;
        return OrderService::where($where)->count();
    }

    public function amount($vnd = false){
        if($vnd){
            return $this->amount * $this->exchange_rate;
        }else{
            return $this->amount;
        }
    }

    /**
     * @author vanhs
     * @desc Ham tinh tong tien hang theo danh sach san pham
     * @param bool $vnd
     * @return int|mixed
     */
    public function amountWithItems($vnd = false){
        $amount = 0;
        $items = $this->item()->get();
        if($items){
            foreach($items as $item){
                if(!$item || !$item instanceof OrderItem){
                    continue;
                }

                $amount_item = $item->getPriceCalculator() * $item->order_quantity;
                if($vnd){
                    $amount_item = $item->getPriceCalculator() * $item->order_quantity * $this->exchange_rate;
                }
                $amount += $amount_item;
            }
        }
        return $amount;
    }

    public function save(array $options = [])
    {
        /**
         * Khi thay doi thong tin don hang (truoc khi da mua) can tinh lai
         * - tien hang
         * - tien dat coc
         * - phi mua hang
         * - phi VC noi dia TQ
         */
        $amount_vnd = $this->amountWithItems(true);
        $amount = $amount_vnd / $this->exchange_rate;

        $deposit_amount_vnd = Cart::getDepositAmount($this->deposit_percent, $amount_vnd);
        $deposit_amount = $deposit_amount_vnd / $this->exchange_rate;

        $data_fee_insert = [
            [ 'name' => 'amount', 'money' => $amount ],
            [ 'name' => 'amount_vnd', 'money' => $amount_vnd ],

            [ 'name' => 'deposit_amount', 'money' => $deposit_amount ],
            [ 'name' => 'deposit_amount_vnd', 'money' => $deposit_amount_vnd ],

            [ 'name' => 'domestic_shipping_fee', 'money' => $this->domestic_shipping_fee ],
            [ 'name' => 'domestic_shipping_fee_vnd', 'money' => $this->domestic_shipping_fee_vnd ],
        ];

        $exist_buying_fee = OrderFee::existFee($this, 'buying_fee');
        if($this->isBeforeStatus(Order::STATUS_BOUGHT, true) || !$exist_buying_fee){
            $buying_fee_vnd = $this->getBuyingFee($amount_vnd);
//            if($this->isFirstOrderThree()){
//                $buying_fee_vnd = 0;
//            }
            $buying_fee = $buying_fee_vnd / $this->exchange_rate;
            $data_fee_insert[] = [ 'name' => 'buying_fee', 'money' => $buying_fee ];
            $data_fee_insert[] = [ 'name' => 'buying_fee_vnd', 'money' => $buying_fee_vnd ];
        }

        OrderFee::createFee($this, $data_fee_insert);

        $this->amount_original_vnd = $this->amount_original * $this->exchange_rate;
        $this->amount_vnd = $amount_vnd;
        $this->customer_amount = $this->amount + $this->domestic_shipping_fee;
        $this->customer_amount_vnd = $this->customer_amount * $this->exchange_rate;
        $this->amount_bargain = $this->customer_amount - $this->amount_original;
        $this->amount_bargain_vnd = $this->customer_amount_vnd - $this->amount_original_vnd;

        //neu khong dien tong gia thuc mua thi don nay coi nhu khong mac ca duoc gi
        if($this->amount_original <= 0){
            $this->amount_bargain = 0;
            $this->amount_bargain_vnd = 0;
        }

        $saved = parent::save($options); // TODO: Change the autogenerated stub

        //after save

        return $saved;
    }

    /**
     * @author vanhs
     * @desc Kiem tra xem don hang hien tai co phai la 3 don hang dau tien hay khong?
     * p/s: Don hang dau tien tinh tu khi bat dau van chuyen ve VN - trang thai van chuyen
     * @return bool
     */
    public function isFirstOrderThree(){
        $number = self::where([
            'user_id' => $this->user_id,
        ])->whereIn('status', [
            self::STATUS_TRANSPORTING,
            self::STATUS_WAITING_DELIVERY,
            self::STATUS_DELIVERING,
            self::STATUS_RECEIVED])
            ->count();

        $order_buying_number_fee = SystemConfig::getConfigValueByKey('order_buying_number_fee');
        if(!$order_buying_number_fee){
            $order_buying_number_fee = $this->order_buying_number_fee;
        }
        if($number < $order_buying_number_fee){
            return true;
        }
        return false;
    }

    /**
     * @author vanhs
     * @desc Chuyen trang thai don sang NhatMinh247 nhan hang
     * @return bool
     */
    public function changeOrderReceivedFromSeller($manualy = false){

        try{
            DB::beginTransaction();

            $create_user = User::find(Auth::user()->id);

            #region -- change status --
            if($this->status == self::STATUS_SELLER_DELIVERY){
                $this->changeStatus(self::STATUS_RECEIVED_FROM_SELLER, false);
                $this->save();

                $status_title_after_change = self::getStatusTitle(self::STATUS_RECEIVED_FROM_SELLER);

                $type_context = Comment::TYPE_CONTEXT_LOG;
                $message_external = sprintf("Đơn hàng chuyển sang trạng thái %s (Đã nhận hàng từ người bán, chuẩn bị vận chuyển về Việt Nam)", $status_title_after_change);
                $message_internal = sprintf("Chuyển trạng thái đơn sang %s", $status_title_after_change);
                if($manualy){
                    $type_context = Comment::TYPE_CONTEXT_ACTIVITY;
                    $message_external = sprintf("Chuyển trạng thái đơn sang %s (Đã nhận hàng từ người bán, chuẩn bị vận chuyển về Việt Nam)", $status_title_after_change);
                    $message_internal = sprintf("Chuyển trạng thái đơn sang %s", $status_title_after_change);
                }

                Comment::createComment($create_user, $this, $message_external, Comment::TYPE_EXTERNAL, $type_context);
                Comment::createComment($create_user, $this, $message_internal, Comment::TYPE_INTERNAL, $type_context);

                $title = "Trạng thái đơn hàng";
                $content = $create_user->name . $message_external;
                CustomerNotification::notificationCustomer($this,$title,$content,'ORDER');


            }
            #endregion

            DB::commit();
            return true;

        }catch(\Exception $e){
            DB::rollback();
            return false;
        }

    }

    /**
     * @author vanhs
     * @desc Kiem tra xem don hang co chon chuyen thang hay khong
     * @return bool
     */
    public function isOrderTransportStraight(){
        return true;
    }

    /**
     * @author vanhs
     * @desc Ham lay dia chi nhan hang day du cua khach hang
     * @return null
     */
    public function getCustomerReceiveAddress(){
        if(empty($this->user_address_id)){
            return null;
        }

        $user_address = UserAddress::find($this->user_address_id);
        if($user_address && $user_address instanceof UserAddress){
            $district = Location::find($user_address->district_id);
            if($district && $district instanceof Location){
                $user_address->district_label = $district->label;
            }
            $province = Location::find($user_address->province_id);
            if($province && $province instanceof Location){
                $user_address->province_label = $province->label;
            }
        }
        return $user_address;
    }

    /**
     * @author vanhs
     * @desc phi van chuyen noi dia TQ - VND
     * @return float
     */
    public function getDomesticShippingFeeVnd(){
        return (float)($this->domestic_shipping_fee * $this->exchange_rate);
    }

    /**
     * @author vanhs
     * @desc Ham cap nhat thong tin don hang
     * @param array $data
     * @return bool
     */
    public function updateInfo($data = []){
        if(count($data)){
            foreach($data as $key => $value){
                $this->$key = $value;
            }
            $this->save();
        }
        return true;
    }

    /**
     * @author vanhs
     * @desc
     * - Chuyen trang thai don sang van chuyen:
     * - Truy thu tien hang con lai + phi mua hang
     * @return bool
     */
    public function changeOrderTransporting(){
        try{
            DB::beginTransaction();
            $create_user = User::find(Auth::user()->id);
            $customer = User::find($this->user_id);

            if($this->status == self::STATUS_RECEIVED_FROM_SELLER){

                $this->changeStatus(self::STATUS_TRANSPORTING, false);
                $this->save();

                $status_title_after_change = self::getStatusTitle(self::STATUS_TRANSPORTING);

                Comment::createComment($create_user, $this, sprintf("Đơn hàng chuyển sang trạng thái %s (Bắt đầu vận chuyển về Việt Nam)", $status_title_after_change), Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_LOG);
                Comment::createComment($create_user, $this, sprintf("Chuyển trạng thái đơn sang %s", $status_title_after_change), Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_LOG);

                #region --tạo notification cho khách--
                $title = 'Trạng thái đơn hàng '.$this->code;
                $content_message = sprintf(" đơn hàng chuyển sang trạng thái %s (Bắt đầu vận chuyển về Việt Nam)", $status_title_after_change);
                $content = $create_user->name.$content_message;
                CustomerNotification::notificationCustomer($this,$title,$content,'ORDER');
                #endregion -- kết thúc tạo notification cho khách --

                $data_fee = OrderFee::$fee_field_order_detail;
                $order_fee = OrderFee::getListFee($this);
                if($order_fee){
                    foreach($order_fee as $order_fee_item){
                        if(!$order_fee_item instanceof OrderFee){
                            continue;
                        }
                        $data_fee[$order_fee_item->name] = 0;
                        if(isset($data_fee[$order_fee_item->name])){
                            $data_fee[$order_fee_item->name] = $order_fee_item->money;
                        }
                    }
                }

                $customer_payment_amount_vnd = UserTransaction::getCustomerPaymentWithOrder($this->id);

                $total_need_payment = (
                        $data_fee['AMOUNT_VND']
                        + $data_fee['DOMESTIC_SHIPPING_FEE_VND']
                        + $data_fee['BUYING_FEE_VND']
                    )
                    - $customer_payment_amount_vnd;
                $total_need_payment = 0 - abs($total_need_payment);

                $message = sprintf('Hệ thống truy thu số tiền hàng còn lại sau khi đặt cọc %sđ; VC nội địa TQ %sđ; Mua hàng %sđ',
                    Util::formatNumber($data_fee['AMOUNT_VND'] - $data_fee['DEPOSIT_AMOUNT_VND']),
                    Util::formatNumber($data_fee['DOMESTIC_SHIPPING_FEE_VND']),
                    Util::formatNumber($data_fee['BUYING_FEE_VND']));

                UserTransaction::createTransaction(
                    UserTransaction::TRANSACTION_TYPE_ORDER_PAYMENT,
                    $message,
                    $create_user,
                    $customer,
                    $this,
                    $total_need_payment
                );

                Comment::createComment($create_user, $this, $message, Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_LOG);
                Comment::createComment($create_user, $this, $message, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_LOG);

                #region --tạo notification cho khách--
                $title = 'Tài chính đơn hàng '.$this->code;
                $content_message = $message;
                $content = $create_user->name.' '.$content_message;
                CustomerNotification::notificationCustomer($this,$title,$content,'ORDER');
                #endregion -- kết thúc tạo notification cho khách --

            }

            DB::commit();
            return true;
        }catch(\Exception $e){
            DB::rollback();

            Log::info('can-not-changeOrderTransporting' . $e->getMessage());

//            throw new Exception($e->getMessage());
            return false;
        }
    }

    /**
     * @author vanhs
     * @desc Chuyen trang thai don sang cho giao hang
     * @return bool
     */
    public function changeOrderWaitingDelivery(){
        try{
            DB::beginTransaction();
            $create_user = User::find(Auth::user()->id);

            #region -- change status --
            if($this->status == self::STATUS_TRANSPORTING){
                $this->changeStatus(self::STATUS_WAITING_DELIVERY, false);
                $this->save();

                $status_title_after_change = self::getStatusTitle(self::STATUS_WAITING_DELIVERY);

                Comment::createComment($create_user, $this, sprintf("Đơn hàng chuyển sang trạng thái %s (Hàng đã về kho phân phối, sẵn sàng giao cho quý khách)", $status_title_after_change), Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_LOG);
                Comment::createComment($create_user, $this, sprintf("Chuyển trạng thái đơn sang %s", $status_title_after_change), Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_LOG);

                #region --tạo notification cho khách--
                $title = 'Trạng thái đơn hàng '.$this->code;
                $content_message = sprintf(" đơn hàng chuyển sang trạng thái %s (Hàng đã về kho phân phối, sẵn sàng giao cho quý khách)", $status_title_after_change);
                $content = $create_user->name.$content_message;
                CustomerNotification::notificationCustomer($this,$title,$content,'ORDER');
                #endregion -- kết thúc tạo notification cho khách --

            }
            #endregion

            DB::commit();
            return true;
        }catch(\Exception $e){
            DB::rollback();
            return false;
        }
    }

    /**
     * @author vanhs
     * @desc Chuyen trang thai don sang dang giao hang
     * @return bool
     */
    public function changeOrderDelivering(){
        try{
            DB::beginTransaction();
            $create_user = User::find(Auth::user()->id);

            #region -- change status --
            if($this->status == self::STATUS_WAITING_DELIVERY){
                $this->changeStatus(self::STATUS_DELIVERING, false);
                $this->save();

                $status_title_after_change = self::getStatusTitle(self::STATUS_DELIVERING);

                Comment::createComment($create_user, $this, sprintf("Đơn hàng chuyển sang trạng thái %s (Hàng đang trên đường đi giao cho quý khách)", $status_title_after_change), Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_LOG);
                Comment::createComment($create_user, $this, sprintf("Chuyển trạng thái đơn sang %s", $status_title_after_change), Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_LOG);

                #region --tạo notification cho khách--
                $title = 'Trạng thái đơn hàng '.$this->code;
                $content_message = sprintf("Đơn hàng chuyển sang trạng thái %s (Hàng đang trên đường đi giao cho quý khách)", $status_title_after_change);
                $content = $create_user->name.$content_message;
                CustomerNotification::notificationCustomer($this,$title,$content,'ORDER');
                #endregion -- kết thúc tạo notification cho khách --
            }
            #endregion

            DB::commit();
            return true;
        }catch(\Exception $e){
            DB::rollback();
            return false;
        }
    }

}
