<?php

namespace App\Console;

use App\Jobs\RestoreStockForExpiredOrders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //to do on linux server terminal:
        // crontab -e
        // * * * * * php /var/www/html/my-project/artisan schedule:run >> /dev/null 2>&1
        $schedule->call(function () {
            app(\App\Services\MediaService::class)->cleanOldTempFiles(60); // e.g., delete files older than 60 mins
        })->hourly();
        $schedule->job(RestoreStockForExpiredOrders::class)->everyFifteenMinutes();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
