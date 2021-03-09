<?php

namespace App\Console\Commands;

use App\Models\User\MerUser;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:test';

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
        $this->userSource();
    }

    public function userSource()
    {
        MerUser::query()->select('id','phone','facebook_auth_code','google_auth_code','wechat_auth_code')->chunkById(100,function ($item){
            $item = $item ? $item->toArray():[];
            foreach ($item as $k => $value){
                if ($value['phone']){
                    $request['reg_source'] = MerUser::REG_SOURCE_PHONE;
                }elseif  (isset($value['facebook_auth_code']) && $value['facebook_auth_code']){
                    $request['reg_source'] = MerUser::REG_SOURCE_FB;
                }elseif (isset($value['google_auth_code']) && $value['google_auth_code']){
                    $request['reg_source'] = MerUser::REG_SOURCE_GOOGLE;
                }elseif (isset($value['wechat_auth_code']) && $value['wechat_auth_code']){
                    $request['reg_source'] = MerUser::REG_SOURCE_WECHAT;
                }
                MerUser::query()->where('id',$value['id'])->update([
                    'reg_source' => $request['reg_source']
                ]);
            }
            usleep(3000);
        });

        return 'success';
    }
}
