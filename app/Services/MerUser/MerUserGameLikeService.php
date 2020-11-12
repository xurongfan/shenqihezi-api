<?php

namespace App\Services\MerUser;

use App\Base\Services\BaseService;

class MerUserGameLikeService extends BaseService
{
    /**
     * @param $gamePackageId
     * @return \App\Base\Services\BaseModel
     */
    public function like($gamePackageId)
    {
        return $this->model->firstOrCreate( [
            'mer_user_id' => $this->userId(),
            'game_package_id' => $gamePackageId
        ]);
    }
}