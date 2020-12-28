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

    /**
     * 我的粉丝
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        return $this->model->query()
            ->where('follow_user_id',$this->userId())
            ->with(['follow' =>function($query){
                $query->where('mer_user_id',$this->userId());
            },'userInfo' => function($query){
                $query->select('id','profile_img','nick_name','sex','vip');
            }])
            ->paginate(20);
    }

    /**
     * 我的关注
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function myFollow()
    {
        return $this->model->query()
            ->where('mer_user_id',$this->userId())
            ->with(['followed' =>function($query){
                $query->where('follow_user_id',$this->userId());
            },'followUserInfo' => function($query){
                $query->select('id','profile_img','nick_name','sex','vip');
            }])
            ->paginate(20);
    }
}