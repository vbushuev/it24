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
        \App\Console\Commands\Catalogs::class,
        \App\Console\Commands\UploadsIT24::class,
        \App\Console\Commands\DownloadsIT24::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:uploads')->everyMinute()->appendOutputTo("/home/a0124380/domains/a0124380.xsph.ru/public_html/storage/logs/command_uploads.log");//->emailOutputTo('yanusdnd@inbox.ru');;
        $schedule->command('command:downloads')->everyMinute();//->emailOutputTo('yanusdnd@inbox.ru');;
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
