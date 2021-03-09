<?php

namespace App\Services\MerUser;

use App\Base\Services\BaseService;
use App\Models\Statics\StaticsRemain;
use App\Models\User\MerUser;
use App\Models\User\MerUserLoginLog;
use Illuminate\Support\Facades\DB;

class MerUserLoginLogService extends BaseService
{
    /**
     * MerUserLoginLogService constructor.
     * @param MerUserLoginLog $model
     */
    public function __construct(MerUserLoginLog $model)
    {
        parent::__construct($model);
    }

    /**
     * 留存率查询
     * @return string
     */
    public function remain(){
        set_time_limit(0);
        ini_set('memory_limit', '200M');

        $remainModel = new StaticsRemain();
        $field = [
            0 => 'dru',
            1 => 'second_day',
            2 => 'third_day',
            3 => 'fourth_day',
            4 => 'fiveth_day',
            6 => 'seventh_day',
            13 => 'fourteenth_day',
            29 => 'thirtieth_day',
        ];
        $date = date('Y-m-d',strtotime('-1 day'));
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

        $res = $this->query("
        SELECT 
        DATEDIFF(last_login_at,register_at) as diff_day,
        DATE_FORMAT(register_at,'%Y-%m-%d') AS register,
        COUNT(DISTINCT mer_user_id) as count FROM mer_user_login_log 
        where last_login_at>='".$date." 00:00:00' 
        AND last_login_at<='".$date." 23:59:59' 
        AND register_at>='2021-03-05 00:00:00' 
        AND DATEDIFF(last_login_at, register_at)<30
        GROUP BY register
        ");
        if ($res){
            foreach ($res as $item){
                isset($field[$item['diff_day']])
                &&
                $remainModel->query()->updateOrCreate([
                    'date' => $item['register']
                ],[
                    $field[$item['diff_day']] => $item['count']
                ]);

            }
        }
        return 'success';

    }
}

