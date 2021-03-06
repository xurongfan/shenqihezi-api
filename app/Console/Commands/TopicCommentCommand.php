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
            'Wow β¦',
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
            'Thatβs so amazing',
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
            'ππ',
            'π',
            'οΏ£β½οΏ£π€―',
            'πππππ',
            'πππ',
            'πππ',
            'πππ',
            'πππ',
            'π',
            'πππ',
            'π₯°',
            'π₯°π₯°π₯°',
            'Omgπ€£π€£π€£',
            'OMGπππ',
            'So funnyπ€£π€£π€£',
            'Fantastic!πππ',
            'Awe!ππ',
            'So good!ππ',
            'This looks so good!ππ',
            'Yum!π₯°π₯°π₯°π₯°',
            'Love it!π₯°π₯°π₯°',
            'Hiπ',
            'Crazyπ₯³π₯³π₯³',
            'Niceπππ',
            'Oh hell yes.πππ',
            'Greatπππ',
            'Hell yeah!!!πππ',
            'YEALπππ',
            'It\'s funnyπππ',
            'Lol that\'s funnyπππ',
            'Amazingππ',
            'pretty!π',
            'π€π€π€',
            'Wow.....π€π€π€π€',
            'Lol that\'s funnyπππ',
            'Hahahaπ₯°π₯°π₯°',
            'This looks so good!ππ',
            'ππππππ',
            'πππππ',
            'β€οΈβ€οΈ',
            'β€οΈ',
            'πππ',
            'ππ'
        ];

        \App\Models\Topic\TopicContent::query()
//            ->whereIn('id',[
//                '1408',
//'1366','1360','1249','1239','1158','1158','1156','1089',
//'1072','998','921','922','857','843','832','824','674','636',
//'622','603','588','587','585','531','490','482', '441' ,'402',
//'402','376' ,'367','358','334','329','254','241','225','179','122','108'
//,'86','69','36'
//            ])
            ->where('is_export',1)->chunk(100,function ($item)use ($content){
            $item = $item->toArray();
            foreach ($item as $key => $value){

                $contentArr = array_random($content,rand(1,count($content)-1));
                shuffle($contentArr);
                $userArr = [];
//                if ($value['id']%3 == 0 || $value['id']%7 == 0)
//                {
                    //θ―θ?Ίε₯εΊ
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
//                }

                for ($i = 0;$i<$value['id']%10;$i++){
                    $userArr[] = rand(9990,10187);
                }
                foreach ($userArr as $user){
                    \App\Models\Topic\TopicContentLike::query()->firstOrCreate([
                        'content_id' => $value['id'],
                        'mer_user_id' => $user
                    ]);
                    \App\Models\User\MerUserFollow::query()->firstOrCreate([
                        'follow_user_id' => $value['mer_user_id'],
                        'mer_user_id' => $user
                    ]);
                }

            }

                return 'success';

            });
        return 'success';
    }
}
