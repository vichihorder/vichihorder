<?php
/**
 * Created by PhpStorm.
 * User: goerge
 * Date: 23/05/2017
 * Time: 15:20
 */

namespace App\Http\Controllers;


use App\Package;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\Input;


class PackageWeightController extends Controller
{
        public function index(){
            return view('input_weight_package',[
                'page_title' => 'Nhập cân nặng hàng hóa',
            ]);
        }

    public function packageWeight(Request $request){

        $barcode = $request->all();


        $packageBarcode = $barcode['packageBarcode'];

        $package_weight = $barcode['packageWeight'];


        $package = Package::retrieveByCode($packageBarcode);

        if($package instanceof Package){
            $package->weight = $package_weight;
            $package->weight_type = 1;
            $package->save();
            return redirect('/package-weight')->with('status','Thanh cong');
        }else{
            return redirect('/package-weight')->with('status','that bai');
        }



    }
}