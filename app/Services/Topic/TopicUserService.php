<?php

namespace App\Services\Topic;

use App\Base\Services\BaseService;

class TopicUserService extends BaseService
{
    /**
     * 用户已关注话题列表
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function userTopic()
    {
        return $this->model->query()
            ->select('id','topic_id')
           ->with(['topic' => function($query){
               $query->select('id','title')->where('status',1);
           }])
            ->whereHasIn('topic',function($query){
                $query->select('id','title')->where('status',1);
            })
            ->where('mer_user_id',$this->userId())
            ->paginate(20);
    }

    /**
     * 关注/取消关注话题
     * @param $topicId
     * @return \App\Base\Services\BaseModel|void
     */
    public function topicFollow($topicId)
    {
        if ($res = $this->findOneBy( [
            'mer_user_id' => $this->userId(),
            'topic_id' => $topicId
        ])) {
            $res->delete();
        }else{
            return $this->save( [
                'mer_user_id' => $this->userId(),
                'topic_id' => $topicId
            ]);
        }
        return ;
    }

}