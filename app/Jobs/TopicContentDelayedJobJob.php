<?php

namespace App\Jobs;

use App\Models\Topic\TopicContent;
use App\Models\Topic\TopicContentDelayedJob;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TopicContentDelayedJobJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 2;   //添加最大尝试次数
    protected $topicContentId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($topicContentId)
    {
        $this->topicContentId = $topicContentId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!config('app.invented_comment')){
            return true;
        }
        try {
            $content = TopicContent::query()->select('id', 'game_package_id', 'extra_info')
                ->where('id',$this->topicContentId)
                ->with(['game' => function ($query) {
                    $query->select('id', 'integral_base');
                }])->first();
            if ($content){
                $type = 1;
                $insertData = [];
                $delayedMaxTime = strtotime("+5 hours");;
                $delayedMinTime = time()+60*1;
                //游戏分享
                if ($content['game_package_id'] && empty($content['extra_info'])){
                    $type = 2;
                }

                //游戏pk
                if ($content['game_package_id'] && isset($content['extra_info']['game_score'])){
                    $type = 3;
                    for ($i=1;$i<=rand(5,19);$i++){
                        $insertData[] = [
                            'topic_content_id' => $this->topicContentId,
                            'content_type' => $type,
                            'delayed_time' => date('Y-m-d H:i:s',rand($delayedMinTime,($i<=2 ? time()+60*10:$delayedMaxTime))),
                            'extra_info' => json_encode([
                                'game_score' => $content['extra_info']['game_score'] ?? 0,
                                'integral_base' => $content['game']['integral_base'] ?? 100,
                            ])
                        ];
                    }
                }

                if (in_array($type,[1,2])){
                    for ($i=1;$i<=rand(5,19);$i++){
                        $insertData[] = [
                            'topic_content_id' => $this->topicContentId,
                            'content_type' => $type,
                            'delayed_time' => date('Y-m-d H:i:s',rand($delayedMinTime,($i<=2 ? time()+60*10:$delayedMaxTime))),
                        ];
                    }
                }
                TopicContentDelayedJob::query()->insert($insertData);
            }
        }catch (\Exception $exception){
            logger('topic_content_delayed_error:'.$exception->getMessage());
        }
        return true;
    }
}
