<?php

namespace App\Services\System;

use App\Base\Services\BaseService;

class SysFeedBackService extends BaseService
{
    /**
     * @param $request
     * @return \App\Base\Services\BaseModel
     */
    public function store($request)
    {
        $request['mer_user_id'] = $this->userId();
        return $this->save($request);
    }
}