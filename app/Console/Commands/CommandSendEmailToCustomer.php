<?php

namespace App\Console\Commands;

use App\Jobs\SendSmsToCustomer;
use App\Library\SendEmail\SendMailToCustomer;
use App\SendEmailCustomerQueue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CommandSendEmailToCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_email_to_customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi mail cho khách thông báo khi hàng về';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // sau khi được lưu trũ du lieu xong thi thư hienj gửi mail ở day
        $list_emails = SendEmailCustomerQueue::where('send_status',SendEmailCustomerQueue::NOT_YET)->paginate(20);
        foreach($list_emails as $item_email ){
            SendMailToCustomer::sendEmailWhenImportPackage($item_email->id,
                $item_email->email,$item_email->content);
        }
    }
}
