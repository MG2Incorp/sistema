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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('CheckProposalsCommand')->hourly()->appendOutputTo(storage_path('logs/proposals.log'));
        $schedule->command('CreateBillingsCommand')->twiceDaily(6, 18)->appendOutputTo(storage_path('logs/billings.log'));
        $schedule->command('CheckBilletsCommand')->hourly()->appendOutputTo(storage_path('logs/billets.log'));
        $schedule->command('CheckAntecipationsCommand')->dailyAt('4:00')->appendOutputTo(storage_path('logs/antecipations.log'));
        $schedule->command('CheckBillingsCommand')->dailyAt('5:00')->appendOutputTo(storage_path('logs/outdateds.log'));
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
