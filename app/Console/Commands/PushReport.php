<?php

namespace App\Console\Commands;

use App\Services\Push\PushResultService;
use Illuminate\Console\Command;

class PushReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:push-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '推送结果报表数据定时';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        set_time_limit(0);
        return app(PushResultService::class)->auto();
    }
}
