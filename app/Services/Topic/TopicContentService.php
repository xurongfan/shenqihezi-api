<?php

namespace App\Services\Topic;

use App\Base\Services\BaseService;

class TopicContentService extends BaseService
{
    /**
     * 发布话题内容
     * @param $request
     * @return \App\Base\Models\BaseModel|\App\Base\Services\BaseModel
     * @throws \Exception
     */
    public function publish($request)
    {
//        if (!isset($request['topic']) || empty($request['topic'])) {
//            throw new \Exception(transL('topic.topic_empty_error'));
//        }
        $request['mer_user_id'] = $this->userId();
        $request['ip'] = getClientIp();
        $this->model->fill($this->model->filter($request))->save();
        $topicService = app(TopicService::class);

        if (isset($request['topic']) && $request['topic']) {
            $topicArr = [];
            foreach ($request['topic'] as $key => $value) {
                $topicArr[] = $topicService->findOrCreate($value)->id;
            }
            $topicArr && $this->model->topic()->sync($topicArr);
            //自动关注此话题
            app(TopicService::class)->follow($topicArr);
        }

        return $this->model;
    }

    /**
     * 话题列表
     * @param int $isFollow 关注人
     * @param int $topicId 话题
     * @param int $isHot 热门
     * @param int $userId 用户
     * @return mixed
     */
    public function index($isFollow = 0,$topicId = 0,$isHot = 0,$userId = 0)
    {
        //已屏蔽用户
        if ($topicId || $isHot) {
            $shiedlUser = app(TopicContentUserShieldService::class)->index($this->userId());
        }
        $shiedlUser = $shiedlUser ?? [];
       $res = $this->model->query()
           ->select('id','mer_user_id','content','image_resource','is_anonymous','position_info','created_at')
           ->with(['user' => function($query){
               $query->select('id','profile_img','nick_name');
           },'topic'=>function($query){
               $query->select('topic.id','topic.title')->where('topic.status',1);
           },'like'=>function($query){
               $query->select('id','content_id')->where('mer_user_id',$this->userId());
           },'IsUserFollow' => function($query){
               $query->where('mer_user_id',$this->userId());
           }])
           //指定话题
           ->when($topicId,function ($query)use($topicId){
               $query->whereHasIn('topic',function ($query) use($topicId){
                   $query->where('topic.id',$topicId)->where('topic.status',1);
               });
           })
           //关注人
           ->when($isFollow,function ($query){
               $query->where('is_anonymous',$this->model::ISANONYMOUS_NO)
                   ->whereHasIn('userFollow',function ($query1){
                       $query1->where('mer_user_id',$this->userId());
                   });
           })
           //热门
           ->when($isHot,function ($query){
               //近一周
               $lately = date('Y-m-d H:i:s',strtotime('-7days'));
               $query->where('created_at','>',$lately)->withCount('comment')->orderBy('comment_count','desc');
           },function ($query){
               $query->orderBy('created_at','desc');
           })
           //指定人
           ->when($userId,function ($query)use ($userId){
               $query->where('mer_user_id',$userId == -1 ? $this->userId() : $userId);
           })
           ->when(isset($shiedlUser) && $shiedlUser ,function ($query) use ($shiedlUser){
               $query->whereNotIn('mer_user_id',$shiedlUser);
           })
           ->withCount(['comment','like'])
           ->paginate(20)
           ->toArray();

       if ($topicId) {
           $topicFollow = app(TopicUserService::class)->findOneBy([
               'topic_id' => $topicId,
               'mer_user_id' => $this->userId(),
           ]);
           $res['topic_follow'] = $topicFollow ? true : false;
       }

       foreach ($res['data'] as $key => &$item){
           //匿名处理
           if (
               $item['is_anonymous'] == $this->model::ISANONYMOUS_YES
               &&
               $this->userId() != $item['mer_user_id']
           ) {
               $item['mer_user_id'] = null;
               $item['user']['id'] = null;
               $item['user']['profile_img'] = null;
               $item['user']['nick_name'] = 'AM';
           }
       }
       return $res;
    }

    /**
     * 话题详情
     * @param $contentId
     * @return \App\Base\Services\BaseModel|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function show($contentId)
    {
        $content = $this->model->newQuery()->where('id',$contentId)
            ->with(['user' => function($query){
                $query->select('id','profile_img','nick_name');
            },'topic'=>function($query){
                $query->select('topic.id','topic.title')->where('topic.status',1);
            }])
            ->withCount('comment')
            ->firstOrFail();
        //匿名处理
        if (
            $content['is_anonymous'] == $this->model::ISANONYMOUS_YES
            &&
            $this->userId() != $content['mer_user_id']
        ) {
            $item['mer_user_id'] = null;
            $item['user']['id'] = null;
            $item['user']['profile_img'] = null;
            $item['user']['nick_name'] = 'AM';
        }


        return $content;
    }

    /**
     * @param $id
     * @return bool|null
     */
    public function deleteContent($id)
    {
        return $this->deleteBy([
            'id' => $id,
            'mer_user_id' => $this->userId()
        ]);
    }

}