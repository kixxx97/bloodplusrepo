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
        Commands\InitiateMedicalHistoryForm::class,
        Commands\NotifyUsersForCampaigns::class,
        Commands\UpdateCampaignToDone::class,
        Commands\UpdateCampaignToOngoing::class,
        Commands\FlushBlood::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        // $schedule->command('campaignToDone')
        //          ->twiceDaily(1, 13);
        // $schedule->command('campaignToOngoing')
        //          ->twiceDaily(1, 13);
        // $schedule->command('initiateMedicalForm')
        //          ->hourly();
        // $schedule->command('notifyUsersCampaign')
        //          ->twiceDaily(1, 13);
        // $schedule->command("flushBlood")
        //          ->twiceDaily(1, 13);
                 
        $schedule->command('campaignToDone')
                 ->everyMinute();
        $schedule->command('campaignToOngoing')
                 ->everyMinute();
        $schedule->command('initiateMedicalForm')
                 ->everyMinute();
        $schedule->command('notifyUsersCampaign')
                 ->everyMinute();
        $schedule->command("flushBlood")
                 ->everyMinute();

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
