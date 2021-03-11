<?php


namespace App\Services\Statics;


use App\Base\Services\BaseService;
use App\Models\Statics\StaticsGame;
use App\Models\User\MerUserGameHistory;
use Illuminate\Support\Facades\DB;

class StaticsGameService extends BaseService
{
    /**
     * StaticsGameService constructor.
     * @param StaticsGame $model
     */
    public function __construct(StaticsGame $model)
    {
        parent::__construct($model);
    }

    /**
     * @return string
     */
    public function runGame()
    {
        set_time_limit(0);
        ini_set('memory_limit', '200M');
        $date = date('Y-m-d',strtotime('-1 day'));
        $model = new MerUserGameHistory();
        $i = 1;
        while (true){
            $res = $model->query()->select(
                DB::raw('COUNT(*) as count'),
                DB::raw("IF(city_name=\"\",'Other',city_name) as city_name"),
                DB::raw("IF(country_code=\"\",'Other',country_code) as country_code"),
                DB::raw("IF(country_name=\"\",'Other',country_name) as country_name"),
                'duration as duration_total',
                'game_package_id',
                DB::raw("('".$date."') as date")
            )->whereBetween('created_at',[$date.' 00:00:00',$date.' 23:59:59' ])
                ->groupBy('country_code')
                ->groupBy('city_name')
                ->groupBy('game_package_id')
                ->forPage($i,100)
                ->get()
                ->toArray();
            if ($res){
                $i++;
                $this->model->query()->insert($res);
            }else{
                break;
            }
        }

        return 'success';

    }
}