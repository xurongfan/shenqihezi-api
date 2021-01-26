<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TopicCommentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'topic:comment';

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
        $content = [
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

        \App\Models\Topic\TopicContent::query()->where('is_export',1)->chunk(50,function ($item)use ($content){
            $item = $item->toArray();
            foreach ($item as $key => $value){

                $contentArr = array_random($content,rand(1,count($content)-1));
                shuffle($contentArr);
                $userArr = [];
                if ($value['id']%3 == 0 || $value['id']%7 == 0)
                {
                    //评论入库
                    foreach ($contentArr as $comment){
                        $userId = rand(9990,10187);//rand(9990,10085);
                        \App\Models\Topic\TopicContentComment::query()->insert([
                            'content_id' => $value['id'],
                            'comment' => $comment,
                            'mer_user_id' => $userId,
                            'created_at' => date('Y-m-d H:i:s',time()-(rand(1,100000))),
                            'updated_at' => date('Y-m-d H:i:s',time())
                        ]);
                        $userArr[] = $userId;
                    }
                }

//                for ($i = 0;$i<$value['id']%10;$i++){
//                    $userArr[] = rand(9990,10187);
//                }
//                foreach ($userArr as $user){
//                    \App\Models\Topic\TopicContentLike::query()->firstOrCreate([
//                        'content_id' => $value['id'],
//                        'mer_user_id' => $user
//                    ]);
//                    \App\Models\User\MerUserFollow::query()->firstOrCreate([
//                        'follow_user_id' => $value['mer_user_id'],
//                        'mer_user_id' => $user
//                    ]);
//                }

            }
        });
        return 'success';
    }
}
