<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

        'App\Console\Commands\SendSmsInfoOrder',
        'App\Console\Commands\CommandSendEmailToCustomer',
        'App\Console\Commands\AutoChangeReceiveOrder',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {


        $schedule->command('send_info_order')
            ->everyMinute();

        $schedule->command('send_email_to_customer')
            ->everyMinute();

        $schedule->command('auto_change_receive_order')
            ->dailyAt('13:00');

//        $schedule->command('auto_change_receive_order')
//            ->everyMinute();
    }


    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
