<?php

namespace App\Services\Topic;

use App\Base\Services\BaseService;
use App\Models\Topic\TopicContent;
use App\Services\Notice\NoticeService;
use Carbon\Carbon;

class TopicContentCommentService extends BaseService
{
    /**
     * 发表评论
     * @param $request
     * @return \App\Base\Models\BaseModel|\App\Base\Services\BaseModel
     */
    public function publish($request)
    {
        $request['mer_user_id'] = $this->userId();
        if (isset($request['pid']) && $request['pid']) {
            $pidInfo = $this->model->newQuery()->where('id' , $request['pid'])->firstOrFail();
            $request['reply_user_id'] = $pidInfo['mer_user_id'];
            $request['pid'] = $pidInfo['pid'] ? $pidInfo['pid'] : $request['pid'] ;
            $request['fid'] = $pidInfo['id'] ?? 0;
        }
        $request['ip'] = getClientIp();
        $this->model->fill($request)->save();

        $content = app(TopicContentService::class)->findOneBy([
            'id' => $request['content_id'] ?? 0
        ]);
        //更新最后评论时间
        $content->update([ 'last_comment_at' => Carbon::now()]);

        app(NoticeService::class)->publish(
            //通知用户
            $pidInfo['mer_user_id'] ?? $content['mer_user_id'],
            1,
            $request['content_id'],
            $this->model->id
        );

        return $this->model;
    }

    /**
     * @param $commentId
     */
    public function deleteComment($commentId)
    {
        if ($this->model->newQuery()
            ->where('mer_user_id',$this->userId())
            ->where('id',$commentId)->delete()){
            $this->model->query()->where('pid',$commentId)->delete();
        }
        return ;

    }

    /**
     * 评论列表
     * @param $contentId
     * @param $pid
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index($contentId,$pid=0)
    {
        $res = $this->model->query()
            ->select('id','pid','mer_user_id','content_id','reply_user_id','like_count','comment','created_at')
            ->where('content_id',$contentId)
            ->when($pid,function ($query){
                $query->with(['replyUser' => function($query1){
                    $query1->select('id','profile_img','nick_name');
                }]);
            },function ($query){
                $query->withCount('childComment')
                    ->with(['childComment' => function($queryComment){
                        $queryComment->select('id','pid','mer_user_id','reply_user_id','like_count','comment','created_at')
                            ->with(['user' => function($query1){
                                $query1->select('id','profile_img','nick_name');
                            }])
                            ->with(['replyUser' => function($query2){
                                $query2->select('id','profile_img','nick_name');
                            }])
                            ->with(['like' => function($query2){
                                $query2->select('id','comment_id')->where('mer_user_id',$this->userId());
                            }])
                            ->orderBy('id','desc')
                            ->take(2);
                    }]);
            })
            ->with(['like' => function($query){
                $query->select('id','comment_id')->where('mer_user_id',$this->userId());
            },'user' => function($query3){
                $query3->select('id','profile_img','nick_name');
            },'content' => function($query4){
                $query4->select('id','is_anonymous','mer_user_id');
            }])
            ->where('pid',$pid ?? 0)
            ->orderBy('like_count','desc')
            ->orderBy('created_at','desc')
            ->paginate(20)
            ->toArray();

        foreach ($res['data'] as $key => &$item) {
            if (
                $this->userId() != $item['content']['mer_user_id']
                &&
                $item['content']['is_anonymous'] == TopicContent::ISANONYMOUS_YES
            ) {
                if (isset($item['child_comment']) && $item['child_comment'] ) {
                    foreach ($item['child_comment'] as $k => &$v) {
                       if ($v['mer_user_id'] == $item['content']['mer_user_id']) {
                           $v['mer_user_id'] = null;
                           $v['user']['id'] = null;
                           $v['user']['profile_img'] = null;
                           $v['user']['nick_name'] = 'AM';
                       }

                       if ($v['reply_user_id'] == $item['content']['mer_user_id']){
                           $v['reply_user_id'] = null;
                           $v['reply_user']['id'] = null;
                           $v['reply_user']['profile_img'] = null;
                           $v['reply_user']['nick_name'] = 'AM';
                       }
                    }
                }

                if (isset($item['user']['id']) && $item['user']['id'] == $item['content']['mer_user_id']) {
                    $item['mer_user_id'] = null;
                    $item['user']['id'] = null;
                    $item['user']['profile_img'] = null;
                    $item['user']['nick_name'] = 'AM';
                }

                if (isset($item['reply_user']['id']) && $item['reply_user']['id'] == $item['content']['mer_user_id']) {
                    $item['reply_user_id'] = null;
                    $item['reply_user']['id'] = null;
                    $item['reply_user']['profile_img'] = null;
                    $item['reply_user']['nick_name'] = 'AM';
                }

                unset($item['content']);

            }
        }

        return $res;
    }

    /**
     * 我的评论
     * @return mixed
     */
    public function myComment()
    {
        return $this->model->query()->select('id','mer_user_id','content_id','comment','created_at')
            ->where('mer_user_id',$this->userId())
            ->with(['user' => function($query){
                $query->select('id','nick_name','profile_img');
            },'content' => function($query){
                $query->select('id','content','image_resource',);
            }])
            ->paginate(20)
            ->toArray();
    }

}