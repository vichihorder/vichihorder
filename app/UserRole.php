<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserRole extends Model
{
    protected $table = 'user_roles';

    public function user()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * @author vanhs
     * @desc Lay danh sach user thuoc 1 nhom
     * @param null $role_id
     * @return null
     */
    public static function findByRoleId($role_id = null){
        if(!$role_id){
            return null;
        }

        $users = DB::table('user_roles')
            ->join('users', 'users.id', '=', 'user_roles.user_id')
            ->select('users.*')
            ->whereIn('user_roles.role_id', $role_id)
            ->orderBy('users.email', 'asc')
            ->get();

        return $users;
    }
}
