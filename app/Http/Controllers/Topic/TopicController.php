<?php

namespace App\Http\Controllers\Topic;

use App\Base\Controllers\Controller;
use App\Services\Topic\TopicService;
use App\Services\Topic\TopicUserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TopicController extends Controller
{
    /**
     * 查找话题
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request)
    {
        $this->validate($request,[
            'keyword' => 'required' ,
        ],[
            'keyword.required' => transL('keyword.keyword_empty_error'),
        ]);
        return app(TopicService::class)->search($request->keyword);
    }

    /**
     * 话题排行列表
     * @return mixed
     */
    public function index()
    {
        return app(TopicService::class)->index();
    }

    /**
     * 用户已关注话题
     * @return mixed
     */
    public function userTopicList()
    {
        return app(TopicUserService::class)->userTopic();
    }

    /**
     * 关注话题
     * @return mixed
     */
    public function follow(Request $request)
    {
        $this->validate($request,[
            'topic_id' => [
                'required' ,
                Rule::exists('topic','id')
            ],
        ],[
            'topic_id.required' => transL('topic.topic_id_empty_error'),
        ]);
        return app(TopicUserService::class)->topicFollow($request->topic_id);
    }
}
