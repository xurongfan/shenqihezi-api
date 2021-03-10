<?php

namespace App\Console\Commands;

use App\Models\Statics\StaticsRemain;
use App\Models\User\MerUser;
use App\Models\User\MerUserInfo;
use App\Services\Statics\StaticsCountryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        try {
            $this->userIpCountry();
        }catch (\Exception $exception){
            logger('error_debug:'.$exception->getMessage());
        }
    }

    /**
     * 清洗用户注册来源
     * @return string
     */
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

    /**
     * 清洗用户注册方式统计
     * @return string
     */
    public function remainSource()
    {
        $dateArr = ['2021-03-05','2021-03-06','2021-03-07'];
        $remainModel = new StaticsRemain();

        foreach ($dateArr as $date){
            //注册方式
            $sourceCount = MerUser::query()
                ->select(DB::raw('count(*) as count'),'reg_source')
                ->where('created_at','>=',$date.' 00:00:00')
                ->where('created_at','<=',$date.' 23:59:59')
                ->groupBy('reg_source')
                ->get()
                ->toArray();
            $remainModel->query()->updateOrCreate([
                'date' => $date
            ],[
                'reg_source' => $sourceCount
            ]);
        }
        return 'success';
    }

    /**
     * 清洗用户国家
     * @return mixed
     */
    public function runCountryHistory()
    {
        return app(StaticsCountryService::class)->runHistory();
    }


    public function userIpCountry(){
        $i = 1;
        while (true){
            $res = MerUser::query()->select('last_login_ip','id' )->with(['userInfo' => function($query){
                        $query->select('country_code','mer_user_id','id');
                    }])
                ->forPage($i,50)
                ->get()->toArray();
            foreach ($res as $item){
                if ($item['last_login_ip']){
                    if (isset($item['user_info']['country_code']) && empty($item['user_info']['country_code'])){
                        $ip = getIp2($item['last_login_ip']);
                        $ip && MerUserInfo::query()->where('mer_user_id',$item['id'])->update($ip);
                    }
                }
            }
            if ($res){
                $i++;

            }else{
                break;
            }
        };

        return 'success';
    }
}
