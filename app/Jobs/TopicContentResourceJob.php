<?php

namespace App\Jobs;

use App\Services\Topic\TopicContentResourceService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TopicContentResourceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contentId;

    public $tries = 3;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($contentId)
    {
        $this->contentId = $contentId;

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        set_time_limit(0);
        if ($this->contentId){
            logger('TopicContentResourceJob:'.$this->contentId);
        }
        return app(TopicContentResourceService::class)->resourceCheck($this->contentId);
    }
}
