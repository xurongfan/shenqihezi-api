<?php

namespace App\Services\MerUser;

use App\Base\Services\BaseService;

class MerRetainLogService extends BaseService
{
    /**
     * @return \App\Base\Services\BaseModel
     */
    public function store()
    {
        return $this->model->newQuery()->firstOrCreate([
            'mer_user_id' => $this->userId(),
            'date' => date('Y-m-d'),
        ]);
    }
}