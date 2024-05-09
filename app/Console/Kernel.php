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
//        $schedule->call(function () {
//            $numbers = Number::all();
//            foreach ($numbers as $number) {
//                $different_days = $number->updated_at->diffInDays(Date::now());
//                if($number->status === 2 && $different_days > 5) {
//                    $number->update([
//                        'status' => 1,
//                        'customer_id' => null
//                    ]);
//                }
//            }
//        })->daily();
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
