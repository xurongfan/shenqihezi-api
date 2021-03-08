<?php

namespace App\Services\MerUser;

use App\Base\Services\BaseService;
use App\Models\Statics\StaticsRemain;
use App\Models\User\MerUserLoginLog;

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

        $dateArr = ['2021-03-05','2021-03-06','2021-03-07'];
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
        foreach ($dateArr as $k => $date){
            $res = $this->query("
        SELECT 
        DATEDIFF(last_login_at,register_at) as diff_day,
        DATE_FORMAT(register_at,'%Y-%m-%d') AS register,
        COUNT(DISTINCT mer_user_id) as count FROM mer_user_login_log 
        where last_login_at>='".$date." 00:00:00' 
        AND last_login_at<='".$date." 23:59:59' 
        AND register_at>='2021-03-05 00:00:00' 
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
        }
        return 'success';

    }
}

