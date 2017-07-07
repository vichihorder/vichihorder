<?php
/**
 * Created by PhpStorm.
 * User: goerge
 * Date: 22/05/2017
 * Time: 11:38
 */

namespace App\Http\Controllers;


use App\Library\SendEmail\SendMailToCustomer;
use App\User;


class SendMailerController extends Controller
{
    
    public function sendEmailToCustomer(){
        $user = User::all();
        $email = [];
        foreach($user as $item_user){
            $email[] = $item_user->email;
        }
        foreach ($email as $item_email){
            SendMailToCustomer::sendMailInfoToCustomer($item_email);
        }

    }
}