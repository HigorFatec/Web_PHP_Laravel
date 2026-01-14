<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('tabela:atualizar')->dailyAt('08:00')->withoutOverlapping();
        //$schedule->command('tabela:atualizar')->everyMinute();

        // $schedule->command('inspire')->hourly();

        $schedule->call(function () {
            \Log::info('CRON ATIVO - '.now());
        })->everyMinute();

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
