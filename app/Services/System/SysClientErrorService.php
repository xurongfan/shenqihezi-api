<?php

namespace App\Services\System;

use App\Base\Services\BaseService;
use App\Models\System\SysClientError;

class SysClientErrorService extends BaseService
{
    /**
     * SysClientErrorService constructor.
     * @param SysClientError $model
     */
    public function __construct(SysClientError $model)
    {
        parent::__construct($model);
    }

    /**
     * @param $request
     * @return \App\Base\Services\BaseModel
     * @throws \GeoIp2\Exception\AddressNotFoundException
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public function store($request)
    {
        $request['ip'] = getClientIp();
        $ipInfo = getIp2($request['ip']);
        $request['country_name'] = $ipInfo['country_name'] ?? '';
        $request['city_name'] = $ipInfo['city_name'] ?? '';
        return $this->save($request);
    }
}