<?php

namespace App\Console\Commands;

use App\Models\Topic\TopicContent;
use Illuminate\Console\Command;

class TopicContentCreatedRandomCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'topic:random';

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
        \App\Models\Topic\TopicContent::query()
            ->where('mer_user_id',10271)
            ->where('id','<=',1290)
            ->where('is_export',1)->chunk(100,function ($item){
                $item = $item->toArray();
                foreach ($item as $key => $value){

                   TopicContent::query()->where('id',$value['id'])->update(
                       ['mer_user_id' => rand(9990,10100)]
                   );

                }


            });

//        $time = strtotime('-30 days');
//        \App\Models\Topic\TopicContent::query()
//            ->where('is_export',1)->chunk(100,function ($item) use ($time){
//                $item = $item->toArray();
//                foreach ($item as $key => $value){
//
//                   TopicContent::query()->where('id',$value['id'])->update(
//                       ['created_at' => date('Y-m-d H:i:s',rand($time,time()+60*60*7))]
//                   );
//
//                }
//
//
//            });
        return 'success';
    }
}
