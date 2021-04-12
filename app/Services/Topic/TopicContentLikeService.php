<?php

namespace App\Services\Topic;

use App\Base\Services\BaseService;
use App\Services\Notice\NoticeService;

class TopicContentLikeService extends BaseService
{
    /**
     * 点赞
     * @param $commentId
     * @return \App\Base\Services\BaseModel|void
     */
    public function like($commentId,$userId = 0,$dispatchNow=false)
    {
        $loginUserId = $userId ? $userId : $this->userId();
        $comment = app(TopicContentService::class)->getModel();
        $content = $comment->query()->where('id',$commentId)->firstOrFail();
        if ($res = $this->findOneBy( [
            'mer_user_id' => $loginUserId,
            'content_id' => $commentId
        ])) {
            $res->delete();

//            $comment->query()->where('id',$commentId)->decrement('like_count');
        }else{
//            $comment->query()->where('id',$commentId)->increment('like_count');
            $res = $this->save( [
                'mer_user_id' => $loginUserId,
                'content_id' => $commentId
            ]);
        }
        app(NoticeService::class)->publish($content['mer_user_id'],2,$commentId,0,$loginUserId,$dispatchNow);

        return $res;
    }
}