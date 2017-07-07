<?php

namespace App\Http\Controllers;

use App\Order;
use App\Permission;
use App\User;
use App\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    protected $orders_cancelled = null;

    function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @author vanhs
     * @desc Tong tien khach da nap
     * @param User $user
     * @return int
     */
    private function __getUserInputMoney(User $user){
        $money = 0;
        $query = DB::table('user_transaction')
            ->select(DB::raw('sum(amount) as amount'))
            ->where([
                ['state', '=', UserTransaction::STATE_COMPLETED],
                ['transaction_type', '=', UserTransaction::TRANSACTION_TYPE_ADJUSTMENT],
                ['user_id', '=', $user->id]
            ])
            ->having('amount', '>', 0)
            ->first();

        if($query){
            $money = $query->amount;
        }
        return $money;
    }

    /**
     * @author vanhs
     * @desc Tong tien hang cua khach
     * @param User $user
     * @return int
     */
    private function __getUserAmount(User $user){
        $money = 0;
        $query = DB::table('order_fee')
            ->select(DB::raw('sum(money) as money'))
            ->where([
                ['name', '=', 'AMOUNT_VND'],
                ['user_id', '=', $user->id]
            ]);
        if($this->orders_cancelled){
            $query = $query->whereNotIn('order_id', explode(',', $this->orders_cancelled));
        }
        $query = $query->first();

        if($query){
            $money = $query->money;
        }
        return $money;
    }

    /**
     * @author vanhs
     * @desc Tong tien khach da dat coc don hang
     * @param User $user
     * @return int
     */
    private function __getUserDeposit(User $user){
        $money = 0;
        $query = DB::table('order_fee')
            ->select(DB::raw('sum(money) as money'))
            ->where([
                ['name', '=', 'DEPOSIT_AMOUNT_VND'],
                ['user_id', '=', $user->id]
            ]);
        if($this->orders_cancelled){
            $query = $query->whereNotIn('order_id', explode(',', $this->orders_cancelled));
        }
        $query = $query->first();
        if($query){
            $money = $query->money;
        }
        return $money;
    }

    /**
     * @author vanhs
     * @desc Thong ke tai chinh theo tung khach hang
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function users(Request $request){
        $can_view = Permission::isAllow(Permission::PERMISSION_STATISTIC_DETAIL);
        if(!$can_view){
            return redirect('403');
        }

        $where = [];
        if(!empty($request->get('user_code'))){
            $where[] = [ 'code', '=', $request->get('user_code') ];
        }

        $where[] = [ 'id', '!=', User::USER_ID_TEST ];
        $users = User::where([
            'section' => User::SECTION_CUSTOMER
        ])
            ->where($where);

        $total_users = $users->count();

        $users = $users->orderBy('id', 'desc')
            ->get();

        $this->orders_cancelled = Order::getOrderIdCancelled();

        if($users){
            foreach($users as $user){
                if(!$user instanceof User){
                    continue;
                }

                $user->input_money_vnd = $this->__getUserInputMoney($user);

                $user->amount_vnd = $this->__getUserAmount($user);

                $user->deposit_vnd = $this->__getUserDeposit($user);

                $user->need_payment_vnd = $user->amount_vnd > $user->deposit_vnd
                    ? $user->amount_vnd - $user->deposit_vnd : 0;
            }
        }

        return view ('statistic_users', [
            'page_title' => 'Thống kê tài chính khách hàng',
            'users' => $users,
            'total_users' => $total_users,
        ]);
    }
}
