<?php

namespace App\Services\Topic;

use App\Base\Services\BaseService;
use App\Jobs\TopicContentDelayedJobJob;
use App\Jobs\TopicContentResourceJob;
use App\Models\Game\GamePackage;
use App\Models\Topic\Topic;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

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
        try {
            $request['mer_user_id'] = $this->userId();
            $request['game_package_id'] = $request['game_package_id'] ?? 0;
            $key = 'topic_content_lock_' . $request['mer_user_id'];
            if (Redis::set($key, 1, 'nx', 'ex', 5)) {
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
                //资源入表
                if (!$request['game_package_id']){
                    app(TopicContentResourceService::class)->resource($this->model->id, $request['image_resource'] ?? []);
                }
                //虚拟评论数据延迟入库
                TopicContentDelayedJobJob::dispatch($this->model->id);

                Redis::del($key);
                return $this->show($this->model->id);
            }
            throw new \Exception(transL('common.system_busy'));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
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
        ], [
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
    public function index($isFollow = 0, $topicId = 0, $isHot = 0, $userId = 0)
    {
        //已屏蔽用户
        if ($this->userId()){
            if (($topicId || $isHot) || (!$isFollow && !$topicId && !$isHot && !$userId)) {
                $shiedlUser = app(TopicContentUserShieldService::class)->index($this->userId());
            }
        }
        $shiedlUser = $shiedlUser ?? [];

        $loginUserId = $this->userId();
        if ($isHot) {
            $redisKey = 'content_push_'.$loginUserId;
            if (Redis::SCARD($redisKey) == 0){
                $hotId = $this->model->query()->select('id', DB::raw('( rand( ) * TIMESTAMP ( now( ) ) ) AS rid '))
                    ->limit(100)
                    ->orderBy(DB::raw('rid'), 'desc')
                    ->pluck('id')
                    ->toArray();
                if ($hotId){
                    Redis::SADD($redisKey,$hotId);
                    Redis::EXPIRE($redisKey,60*60);
                }

            }
            $hotId = Redis::SPOP($redisKey,20);
        }
        $hotId = $hotId ?? [];

        $res = $this->model->query()
            ->select('id', 'mer_user_id', 'content', 'image_resource', 'is_anonymous', 'position_info', 'created_at', 'game_package_id', 'extra_info','is_export')
            ->with(['user' => function ($query) {
                $query->select('id', 'profile_img', 'nick_name', 'vip');
            }, 'topic' => function ($query) {
                $query->select('topic.id', 'topic.title')->where('topic.status', 1);
            }, 'game' => function ($query) {
                $query->select('id', 'title', 'icon_img', 'background_img', 'url', 'is_crack', 'crack_url', 'crack_des', 'status', 'des', 'video_url', 'is_rank', 'is_landscape');
            }])
           ->when($loginUserId,function ($query) use ($loginUserId){
               $query->with([
                   'like' => function ($query1) use($loginUserId) {
                       $query1->select('id', 'content_id')->where('mer_user_id',$loginUserId);
                   }, 'IsUserFollow' => function ($query1) use($loginUserId){
                       $query1->where('mer_user_id', $loginUserId);
                   }
               ]);
           })
            //指定话题
            ->when($topicId, function ($query) use ($topicId) {
                $query->whereHasIn('topic', function ($query1) use ($topicId) {
                    $query1->where('topic.id', $topicId)->where('topic.status', 1);
                });
            })
            //关注人
            ->when($isFollow, function ($query) use($loginUserId){
                $query->where('is_anonymous', $this->model::ISANONYMOUS_NO)
                    ->whereHasIn('userFollow', function ($query1) use($loginUserId){
                        $query1->where('mer_user_id', $loginUserId);
                    });
            })
           //热门
           ->when($isHot,function ($query) use ($hotId){
               $query->whereIn('id',$hotId);
//               //近一周
//               $lately = date('Y-m-d H:i:s',strtotime('-7days'));
//               $query->where('created_at','>',$lately)->withCount('comment')->orderBy('comment_count','desc');
           },function ($query){
               $query->orderBy('created_at','desc');
           })
            //指定人
            ->when($userId, function ($query) use ($userId,$loginUserId) {
                $query->where('mer_user_id', $userId == -1 ? $loginUserId : $userId);
                if ($userId > 0) {
                    $query->where('is_anonymous', $this->model::ISANONYMOUS_NO);
                }
            })
            ->when(isset($shiedlUser) && $shiedlUser, function ($query) use ($shiedlUser) {
                $query->whereNotIn('mer_user_id', $shiedlUser);
            })
            ->withCount(['comment', 'like'])
            ->paginate(20,'[*]','page',$isHot?1:null)
            ->toArray();

        if ($topicId && $loginUserId) {
            $topicFollow = app(TopicUserService::class)->findOneBy([
                'topic_id' => $topicId,
                'mer_user_id' => $loginUserId,
            ]);
            $res['topic_follow'] = $topicFollow ? true : false;
        }

        foreach ($res['data'] as $key => &$item) {
            //匿名处理
            if (
                $item['is_anonymous'] == $this->model::ISANONYMOUS_YES
                &&
                $loginUserId != $item['mer_user_id']
            ) {
                $item['mer_user_id'] = null;
                $item['user']['id'] = null;
                $item['user']['profile_img'] = null;
                $item['user']['nick_name'] = 'AM';
            }
            if ($isHot) {
                $item['created_at'] = null;
            }
            if ($item['is_export']){
                $item['like_count'] = rand(10,1000);
            }
        }

        if ($isHot && $hotId){
//            $res['data'] = array_column($res['data'],null,'id');
            $hotId = array_flip($hotId);
            foreach ($res['data'] as $key => &$datum){
                $datum['index'] = $hotId[$datum['id']];
            }
            $res['data'] = my_array_multisort($res['data'],'index');
            $res['last_page'] = 100;
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
        $loginUserId = $this->userId();
        $content = $this->model->newQuery()->where('id', $contentId)
            ->with(['user' => function ($query) {
                $query->select('id', 'profile_img', 'nick_name');
            }, 'topic' => function ($query) {
                $query->select('topic.id', 'topic.title')->where('topic.status', 1);
            }, 'IsUserFollow' => function ($query) {
                $query->where('mer_user_id', $this->userId());
            }, 'game' => function ($query) {
                $query->select('id', 'title', 'icon_img', 'background_img', 'url', 'is_crack', 'crack_url', 'crack_des', 'status', 'des', 'video_url', 'is_rank', 'is_landscape');
            }])
            ->when($loginUserId,function ($query) use ($loginUserId){
                $query->with(['like' => function ($query1) use($loginUserId){
                    $query1->select('id', 'content_id')->where('mer_user_id', $loginUserId);
                }]);
            })
            ->withCount(['comment', 'like'])
            ->firstOrFail();
        //匿名处理
        if (
            $content['is_anonymous'] == $this->model::ISANONYMOUS_YES
            &&
            $loginUserId != $content['mer_user_id']
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
        $topic = Cache::remember('GameTopic', 60 * 6, function () {
            return Topic::query()->firstOrCreate([
                'title' => 'Fun Touch Game'
            ]);
        });

        $gameList = Cache::remember('funTouchGame', 60 * 2, function () {
            $count = GamePackage::query()->count();
            $gameList = [];
            while (count($gameList) < 3) {
                if (!in_array($offset = mt_rand(1, $count), array_column($gameList, 'id'))) {
                    $gameList[] = GamePackage::query()->select('id', 'icon_img')->where('id', '>=', $offset)->first();
                }
            }
            return $gameList;
        });

        return compact('topic', 'gameList');
    }

}