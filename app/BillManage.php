<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BillManage extends Model
{
    protected $table = 'bill_manage';

    public static function getCode(){
        $query = DB::select("select id from bill_manage order by id desc limit 1;");
        if($query){
            $number = (int)$query[0]->id;
        }else{
            $number = 0;
        }

        $row = true;
        while($row){
            $number++;
            $code = sprintf("P%s", $number);
            $row = self::where([
                ['code', '=', $code]
            ])->first();
        }
        return $code;
    }
}
