<?php

namespace App\Services\Statics;

use App\Base\Services\BaseService;
use App\Models\Statics\StaticsCountry;

class StaticsCountryService extends BaseService
{
    /**
     * StaticsCountryService constructor.
     * @param StaticsCountry $model
     */
    public function __construct(StaticsCountry $model)
    {
        parent::__construct($model);
    }

    /***
     * @return string
     */
    public function runHistory(){
        set_time_limit(0);
        ini_set('memory_limit', '200M');

        $dateArr = ['2021-03-05','2021-03-06','2021-03-07','2021-03-08','2021-03-09'];
        foreach ($dateArr as $date){
            $res = $this->query('SELECT COUNT(*) as total,city_name,country_name,country_code FROM `mer_user_info` WHERE created_at>="'.$date.' 00:00:00" AND created_at <="'.$date.' 23:59:59" GROUP by country_code,city_name');
            if ($res){
                foreach ($res as $item){
                    $item['city_name'] = $item['city_name']?$item['city_name']:'other';
                    $item['country_name'] = $item['country_name']?$item['country_name']:'other';
                    $item['country_code'] = $item['country_code']?$item['country_code']:'other';
                    $item['date'] = $date;
                    $this->model->query()->insert($item);
                }
            }
            usleep(2000);
        }

        return 'success';
    }
}