<?php

namespace App\Console\Commands;

use App\Models\Topic\TopicContentDelayedJob;
use App\Models\User\MerUser;
use App\Services\Topic\TopicContentCommentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class TopicContentDelayedJobCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comment:delayed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        TopicContentDelayedJob::query()
            ->where('status',0)
            ->where('delayed_time','<=',date('Y-m-d H:i:s'))
            ->chunkById(100,function ($data) {
                $data = $data->toArray();
                if ($data){
                    foreach ($data as $key => $datum){
                        $commentUserId = $this->randExportUser();
                        if (!$commentUserId){
                            continue;
                        }
                        try {
                            switch ($datum['content_type']){
                                case 1:
                                    $comment = $this->getComment($datum['content_type']);
                                    break;
                                case 2:
                                    $comment = $this->getComment($datum['content_type']);
                                    break;
                                case 3:
                                    if (isset($datum['extra_info']['game_score']) && isset($datum['extra_info']['integral_base'])){
                                        $score = rand($datum['extra_info']['integral_base']/10,$datum['extra_info']['integral_base']);
                                        if ($score == $datum['extra_info']['game_score']){
                                            $comment = "I am good as you! I got ".$score." !";
                                        }else{
                                            $comment = $score > $datum['extra_info']['game_score'] ?
                                                ("<img src=\"success\">&nbsp;&nbsp;I beat you! I got <font color=#FF4E67>".$score."</font> !")
                                                :
                                                ("<img src=\"failure\">&nbsp;&nbsp;Wow! You are so good . I got ".$score." .");
                                        }

                                    }
                                    break;
                                default:
                                    break;
                            }

                            app(TopicContentCommentService::class)->publish(
                                [
                                    'content_id' => $datum['topic_content_id'],
                                    'pid' => 0,
                                    'comment' => $comment,
                                    'created_at' => $datum['delayed_time']
                                ],
                                $commentUserId,
                                true
                            );


                        }catch (\Exception $exception){
                            $error = 'topicContentDelayedJobCommand error:'.$exception->getMessage().'<br>'.$exception->getFile().'<br>'.$exception->getLine();
                        }
                        TopicContentDelayedJob::query()->where('id',$datum['id'])->update([
                            'status' => isset($error)?0:1,
                            'run_time' => date('Y-m-d H:i:s'),
                            'error' => $error ?? ''
                        ]);
                        usleep(500);
                    }
                }
            });
    }

    /**
     * 获取导入随机用户id
     * @return array
     */
    private function randExportUser()
    {
        $redisKey = 'exportUser';
        if (Redis::SCARD($redisKey) == 0){
            $randUserList = MerUser::query()->where('is_export',1)->pluck('id')->toArray();
            if ($randUserList){
                Redis::SADD($redisKey,$randUserList);
                Redis::EXPIRE($redisKey,60*60*5);
            }
        }
        return Redis::SRANDMEMBER($redisKey,1)[0];
    }

    /**
     * @param $type
     * @return mixed
     */
    private function getComment($type)
    {
        $dynamicComment1 =  [
            'Nah..........hhhh',
            'So freaking cure!!!!',
            'Crazy',
            'that\'s funny',
            'i love it ',
            'GOOD',
            'Hahaha',
            'Lol',
            'Yep!',
            'Wow …',
            'pretty!',
            'Amazing',
            'Lol that\'s funny',
            'Brilliant!',
            ' It\'s funny',
            'Hell yeah!!!',
            'Hahahaha!!!',
            'Great',
            'Oh hell yes.',
            'Nice',
            'Crazy!',
            'Fantastic',
            'That’s so amazing',
            'So beautiful.',
            'That\'s beautiful!!',
            'So pretty!',
            'Looks so good!!!',
            'Oh wow that looks really really good!',
            'Looks amazing',
            'Looks awesome!',
            'Yum',
            'That looks sooooooo yummy!!!',
            'Hi',
            'Yum!',
            ' This looks so good!!',
            'so good!',
            'Awe!',
            'Fantastic',
            'So funny',
            'Omg!',
            'Hey',
            'OhOhOh',
            'funny!!!',
            'Lol funny!!!',
            'Very funny!!!',
            '😄😂',
            '😘',
            '￣▽￣🤯',
            '😍😍😍😍😍',
            '👍👍👍',
            '👏👏👏',
            '😃😃😃',
            '😂😂😂',
            '🙂',
            '😋😋😋',
            '🥰',
            '🥰🥰🥰',
            'Omg🤣🤣🤣',
            'OMG😂😂😂',
            'So funny🤣🤣🤣',
            'Fantastic!👏👏👏',
            'Awe!😋😋',
            'So good!😝😝',
            'This looks so good!😎😎',
            'Yum!🥰🥰🥰🥰',
            'Love it!🥰🥰🥰',
            'Hi😃',
            'Crazy🥳🥳🥳',
            'Nice👏👏👏',
            'Oh hell yes.😃😃😃',
            'Great👍👍👍',
            'Hell yeah!!!🙂🙂🙂',
            'YEAL👏👏👏',
            'It\'s funny😎😎😎',
            'Lol that\'s funny😂😂😂',
            'Amazing😋😋',
            'pretty!😆',
            '🤓🤓🤓',
            'Wow.....🤓🤓🤓🤓',
            'Lol that\'s funny😂😂😂',
            'Hahaha🥰🥰🥰',
            'This looks so good!😎😎',
            '😎😎😎😋😋😋',
            '😜😜😝😝😝',
            '❤️❤️',
            '❤️',
            '😊😊😊',
            '😊😊'
        ];

        $dynamicComment2 =  [
            'Who will play with me？',
            'have a try',
            'I like this way of playing',
            'cool',
            'i love it ',
            'I like the color',
            'My brother likes it very much',
            'How to play',
            'Yep!',
            'It looks good',
            'This game is a little hard for me',
            'I also have a fun game',
            '[爱心][爱心]',
            '[强]',
            ' It\'s funny',
            'Hell yeah!!!',
            '[耶][耶]',
            'Great',
            '[强].',
            'I always fail',
            'What else',
            'I love playing',
            'That’s so amazing',
            'How did you find out about this game',
            'love it',
            'So pretty!',
            'Looks so good!!!',
            'Oh wow that looks really really good!',
            'Looks amazing',
            'Looks awesome!',
            'See my dynamic also have fun game',
            'That looks sooooooo yummy!!!',
            'Hi',
            'It\'s a game I like',
            ' This looks so good!!',
            'so good!',
            'I want to be your friend',
            'Fantastic',
            'So funny',
            'Omg!',
        ];

        return ${'dynamicComment'.$type}[array_rand(${'dynamicComment'.$type})];
    }
}
