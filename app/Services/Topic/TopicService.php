<?php

namespace App\Services\Topic;

use App\Base\Services\BaseService;
use App\Models\Topic\TopicUser;

class TopicService extends BaseService
{
    /**
     * 根据标题查找或创建
     * @param $title
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findOrCreate($title,$userId = 0)
    {
        return $this->model->query()->firstOrCreate(
            [
                'title' => $title
            ],
            [
                'mer_user_id' => $userId ? $userId : $this->userId()
            ]
        );
    }

    /**
     * 用户已关注话题ID
     * @return \App\Base\Services\Collection
     */
    public function userTopic()
    {
        return $this->user()->topic->pluck('topic_id')->toArray();
    }

    /**
     * 自动关注话题
     * @param $topic
     */
    public function follow($topic)
    {
        $topic = is_array($topic) ? $topic : [$topic];

        $userTopic = $this->userTopic();

        foreach ($topic as $k => $v){
            if (!in_array($v,$userTopic)) {
                TopicUser::query()->firstOrCreate([
                    'topic_id' => $v,
                    'mer_user_id' => $this->userId()
                ]);
            }
        }

        return ;
    }

    /**
     * 查找话题
     * @param $title
     * @return mixed
     */
    public function search($title)
    {
        return $this->model->select('id','title')->where('title','like','%'.$title.'%')->where('status',1)->get();
    }

    /**
     * 话题排行列表
     * @return mixed
     */
    public function index()
    {
        return $this->model->select('id','title')
            ->withCount('topicContent')
            ->orderBy('topic_content_count','desc')
            ->take(10)
            ->get();
    }
}