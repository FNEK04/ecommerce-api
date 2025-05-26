<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\CheckExpiredOrders::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Проверяем просроченные заказы каждые 2 минуты
        $schedule->command('orders:check-expired')
            ->everyTwoMinutes()
            ->withoutOverlapping()
            ->runInBackground();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}