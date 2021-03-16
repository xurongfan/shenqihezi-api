<?php


namespace App\Services\Game;


use App\Base\Services\BaseService;
use App\Models\Game\GameTypeClickLog;

class GameTypeClickLogService extends BaseService
{
    /**
     * GameTypeClickLogService constructor.
     * @param GameTypeClickLog $model
     */
    public function __construct(GameTypeClickLog $model)
    {
        parent::__construct($model);
    }

    /**
     * @param $gameTypeId
     * @return \App\Base\Services\BaseModel
     * @throws \GeoIp2\Exception\AddressNotFoundException
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public function store($gameTypeId)
    {
        $ipInfo = getIp2();
        return $this->save([
            'mer_user_id' => $this->userId(),
            'game_type_id' => $gameTypeId,
            'country_code' => $ipInfo['country_code'] ?? '',
            'country_name' => $ipInfo['country_name'] ?? '',
            'city_name' => $ipInfo['city_name'] ?? '',
        ]);
    }
}