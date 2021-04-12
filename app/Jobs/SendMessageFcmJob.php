<?php

namespace App\Jobs;

use App\Models\Message\MessageFcm;
use App\Models\User\MerUserInfo;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMessageFcmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;   //添加最大尝试次数
    protected $merUserId;
    protected $title;
    protected $content;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($merUserId,$title,$content)
    {
        $this->merUserId = $merUserId;
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $info = MerUserInfo::query()->where('mer_user_id',$this->merUserId)->select('fcm_id')->first();

            logger('send message:'.json_encode($info));


            if (isset($info['fcm_id']) && $info['fcm_id']) {
                $res = getHttpContent('post','http://47.242.85.154:81/api/message-send',[
                    'to_id' => $info['fcm_id'],
                    'title' => $this->title,
                    'body' => $this->content
                ]);
                $res = json_decode($res,true);
                logger('send message result:'.json_encode($res));

                if (isset($res['success']) && $res['success']){
                    MessageFcm::query()->create([
                        'mer_user_id' => $this->merUserId,
                        'title' => $this->title,
                        'content' => $this->content,
                        'to_id' => $info['fcm_id'],
                        'message_id' => $res['results'][0]['message_id']??'',
                    ]);
                }
            }

        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
        return true;
    }
}
