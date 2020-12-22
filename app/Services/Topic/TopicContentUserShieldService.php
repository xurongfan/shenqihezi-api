<?php

namespace App\Services\Topic;

use App\Base\Services\BaseService;

class TopicContentUserShieldService extends BaseService
{
    /**
     * @param $shieldUserId
     * @return \App\Base\Services\BaseModel
     */
    public function store($shieldUserId)
    {
        if ($res = $this->findOneBy( [
            'mer_user_id' => $this->userId(),
            'shield_user_id' => $shieldUserId
        ])) {
            $res->delete();
        }else{
            $res = $this->save( [
                'mer_user_id' => $this->userId(),
                'shield_user_id' => $shieldUserId
            ]);
        }
        return $res;
    }

    /**
     * 用户屏蔽列表
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    public function index($userId = 0)
    {
        return $this->model->query()->where('mer_user_id',$userId ? $userId : $this->userId())->pluck('shield_user_id');
    }
}