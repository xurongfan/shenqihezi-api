<?php

namespace App\Services\MerUser;

use App\Base\Services\BaseService;

class MerUserFollowService extends BaseService
{
    /**
     * 关注/取消关注
     * @param $commentId
     * @return \App\Base\Services\BaseModel|void
     */
    public function follow($commentId)
    {
        if ($res = $this->findOneBy( [
            'mer_user_id' => $this->userId(),
            'follow_user_id' => $commentId
        ])) {
            $res->delete();
        }else{
            return $this->save( [
                'mer_user_id' => $this->userId(),
                'follow_user_id' => $commentId
            ]);
        }
        return ;
    }
}