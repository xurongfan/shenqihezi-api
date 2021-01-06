<?php

namespace App\Console\Commands;

use App\Services\Topic\TopicContentResourceService;
use Illuminate\Console\Command;

class TopciContentResourceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:content-resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '定时检测违规图片';

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
        logger('定时检测违规图片'.date('Y-m-d H:i:s'));
        return app(TopicContentResourceService::class)->resourceCheck();
    }
}
