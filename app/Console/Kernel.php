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
        $schedule->command('telegram:sendquote BOFHers buenosdias')
            ->weekdays()
            ->timezone('Europe/Madrid')
            ->dailyAt('8:30');

        $schedule->command('telegram:sendquote BOFHers')
            ->hourlyAt(30)
            ->timezone('Europe/Madrid')
            ->between('9:00', '23:00');

        $schedule->command('telegram:sendquote BOFHers_Gamers')
            ->dailyAt('18:30')
            ->timezone('Europe/Madrid');
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
