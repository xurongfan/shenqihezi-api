<?php

namespace App\Services\Game;

use App\Base\Services\BaseService;

class AdBuryingService extends BaseService
{
    /**
     * @param $packageId
     * @param $type
     * @param $deviceUid
     * @param $showType
     * @return \App\Base\Services\BaseModel
     */
    public function report($packageId,$type,$deviceUid,$showType)
    {
        return $this->save([
            'package_id' => $packageId,
            'type' => $type,
            'show_type' => $showType,
            'device_uid' => $deviceUid,
            'ip' => getClientIp()
        ]);
    }
}
