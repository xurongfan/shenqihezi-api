<?php

namespace App\Console;

use App\Console\Commands\AddFacade;
use App\Console\Commands\AddServices;
use App\Console\Commands\MerUserLoginRemainCommand;
use App\Console\Commands\StaticsCountryCommand;
use App\Console\Commands\TestCommand;
use App\Console\Commands\TopicCommentCommand;
use App\Console\Commands\TopicContentCreatedRandomCommand;
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
        AddFacade::class,
        AddServices::class,
        TopicCommentCommand::class,
        TopicContentCreatedRandomCommand::class,
        MerUserLoginRemainCommand::class,
        StaticsCountryCommand::class,
        TestCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:content-resource')
        ->everyFiveMinutes();//每五分钟执行一次

        $schedule->command('login:remain')
            ->dailyAt('01:00');;//留存计算

        $schedule->command('statics:country')
            ->dailyAt('02:00');;//用户注册国家分布
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
