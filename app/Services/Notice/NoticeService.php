<?php

namespace App\Services\Notice;

use App\Base\Services\BaseService;
use App\Services\Topic\TopicContentCommentService;

class NoticeService extends BaseService
{
    /**
     * @param $merUserId
     * @param $type
     * @param $contentId
     * @param $commentId
     * @return \App\Base\Services\BaseModel|void
     */
    public function publish($merUserId,$type,$contentId=0,$commentId=0)
    {
        if ($merUserId == $this->userId()) {
            return false;
        }
        if (empty($contentId) && $commentId) {
            $contentInfo =  app(TopicContentCommentService::class)->findOneBy([
                'id' => $commentId
            ]);
            $contentId = $contentInfo['content_id'] ?? 0;
        }
        if ($res = $this->findOneBy( [
            'originate_user_id' => $this->userId(),
            'mer_user_id' => $merUserId,
            'type' => $type,
            'content_id' => $contentId,
            'comment_id' => $commentId
        ])) {
            $res->delete();
        }else{
            return $this->save( [
                'originate_user_id' => $this->userId(),
                'mer_user_id' => $merUserId,
                'type' => $type,
                'content_id' => $contentId,
                'comment_id' => $commentId
            ]);
        }
        return ;
    }

    /**
     * 我的话题通知
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        $result = $this->model->query()
            ->where('mer_user_id',$this->userId())
            ->with(['user' => function($query){
                $query->select('id','nick_name','profile_img');
            }])
            ->with(['content' => function($query){
                $query->select('id','mer_user_id','content','image_resource','is_anonymous')
                    ->where('status',1);
            }])
            ->with(['comment' => function($query){
                $query->select('id','pid','mer_user_id','reply_user_id','comment')
                    ->with(['parentComment' => function($query1){
                        $query1->select('id','pid','mer_user_id','reply_user_id','comment')
                        ->with(['user' => function($query11){
                            $query11->select('id','nick_name','profile_img');
                        },'replyUser' => function($query12){
                            $query12->select('id','nick_name','profile_img');
                        }]);
                    },'user' => function($query2){
                        $query2->select('id','nick_name','profile_img');
                    },'replyUser' => function($query3){
                        $query3->select('id','nick_name','profile_img');
                    }]);

            }])
            ->paginate(20)
            ->toArray();

        foreach ($result['data'] as $key => &$datum) {
            if (isset($datum['content']['is_anonymous']) && $datum['content']['is_anonymous']) {
                //评论通知用户匿名处理
                if ($datum['type'] == 1 && $datum['user']['id'] == $datum['content']['mer_user_id']) {
                    $datum['user']['id'] = null;
                    $datum['user']['nick_name'] = 'AM';
                    $datum['user']['profile_img'] = null;
                }

                if (isset($datum['comment']['reply_user'])
                    &&
                    $datum['comment']['reply_user']
                    &&
                    $datum['content']['mer_user_id'] == $datum['comment']['reply_user']['id']
                ) {
                    $datum['comment']['reply_user']['id'] = null;
                    $datum['comment']['reply_user']['nick_name'] = 'AM';
                    $datum['comment']['reply_user']['profile_img'] = null;
                }

                if (
                    isset($datum['comment']['user'])
                    &&
                    $datum['comment']['user']
                    &&
                    $datum['content']['mer_user_id'] == $datum['comment']['user']['id']
                ) {
                    $datum['comment']['user']['id'] = null;
                    $datum['comment']['user']['nick_name'] = 'AM';
                    $datum['comment']['user']['profile_img'] = null;
                }

                if (isset($datum['comment']['parent_comment']['reply_user'])
                    &&
                    $datum['comment']['parent_comment']['reply_user']
                    &&
                    $datum['content']['mer_user_id'] == $datum['comment']['parent_comment']['reply_user']['id']
                ) {
                    $datum['comment']['parent_comment']['reply_user']['id'] = null;
                    $datum['comment']['parent_comment']['reply_user']['nick_name'] = 'AM';
                    $datum['comment']['parent_comment']['reply_user']['profile_img'] = null;
                }

                if (isset($datum['comment']['parent_comment']['user'])
                    &&
                    $datum['comment']['parent_comment']['user']
                    &&
                    $datum['content']['mer_user_id'] == $datum['comment']['parent_comment']['user']['id']
                ) {
                    $datum['comment']['parent_comment']['user']['id'] = null;
                    $datum['comment']['parent_comment']['user']['nick_name'] = 'AM';
                    $datum['comment']['parent_comment']['user']['profile_img'] = null;
                }

            }

            if ($datum['status'] == 1) {
                $arr[] = $datum['id'];
            }
        }
        //更新未读状态
        if (isset($arr) && $arr) {
            $this->model->query()->whereIn('id',$arr)->update([
                'status' => 0
            ]);
        }
        return $result;
    }

    /**
     * @return int
     */
    public function noticeCount()
    {
        return $this->model->query()
            ->where('status',1)
            ->where('mer_user_id',$this->userId())
            ->count();
    }

}