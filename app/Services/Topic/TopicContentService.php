<?php

namespace App\Services\Topic;

use App\Base\Services\BaseService;
use App\Jobs\TopicContentResourceJob;
use App\Models\Game\GamePackage;
use App\Models\Topic\Topic;
use Illuminate\Support\Facades\Cache;

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
        //资源入驻
        app(TopicContentResourceService::class)->resource($this->model->id,$request['image_resource']??[]);

        return $this->model;
    }

    /**
     * 取消匿名
     * @param $contentId
     * @return bool
     */
    public function cancelAnonymous($contentId)
    {
        return $this->updateBy([
            'id' => $contentId,
            'mer_user_id' => $this->userId()
        ],[
            'is_anonymous' => 0
        ]);
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
        if (($topicId || $isHot) || (!$isFollow && !$topicId && !$isHot && !$userId)) {
            $shiedlUser = app(TopicContentUserShieldService::class)->index($this->userId());
        }
        $shiedlUser = $shiedlUser ?? [];

       $res = $this->model->query()
           ->select('id','mer_user_id','content','image_resource','is_anonymous','position_info','created_at','game_package_id')
           ->with(['user' => function($query){
               $query->select('id','profile_img','nick_name','vip');
           },'topic'=>function($query){
               $query->select('topic.id','topic.title')->where('topic.status',1);
           },'like'=>function($query){
               $query->select('id','content_id')->where('mer_user_id',$this->userId());
           },'IsUserFollow' => function($query){
               $query->where('mer_user_id',$this->userId());
           },'game' => function($query){
               $query->select('id','title','icon_img','background_img','url','is_crack','crack_url','crack_des','status','des','video_url');
           }])
//           ->when($gameId,function ($query){
//               $query->where('game_package_id','!=',0);
//           })
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
               if($userId > 0){
                   $query->where('is_anonymous',$this->model::ISANONYMOUS_NO);
               }
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
           if ($isHot){
               $item['created_at'] = null;
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
            },'like'=>function($query){
                $query->select('id','content_id')->where('mer_user_id',$this->userId());
            },'IsUserFollow' => function($query){
                $query->where('mer_user_id',$this->userId());
            },'game' => function($query){
                $query->select('id','title','icon_img','background_img','url','is_crack','crack_url','crack_des','status','des','video_url');
            }])
            ->withCount(['comment','like'])
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

    /**
     * @return array
     */
    public function gameTopic()
    {
        $topic = Cache::remember('GameTopic',60*6,function (){
            return Topic::query()->firstOrCreate([
                'title' => 'Fun Touch Game'
            ]);
        });

        $gameList = Cache::remember('funTouchGame',60*2,function (){
            $count = GamePackage::query()->count();
            $gameList = [];
            while (count($gameList) < 3){
                if (!in_array($offset = mt_rand(1,$count),array_column($gameList,'id'))){
                    $gameList[] = GamePackage::query()->select('id','icon_img')->where('id','>=',$offset)->first();
                }
            }
            return $gameList;
        });

        return compact('topic','gameList');
    }

}