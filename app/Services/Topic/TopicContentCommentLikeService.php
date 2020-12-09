<?php

namespace App\Services\Topic;

use App\Base\Services\BaseService;
use App\Services\Notice\NoticeService;

class TopicContentCommentLikeService extends BaseService
{
    /**
     * @param $commentId
     * @return \App\Base\Services\BaseModel|void
     */
    public function like($commentId)
    {
        $comment = app(TopicContentCommentService::class)->getModel();
        $info = $comment->where('id',$commentId)->select('id','mer_user_id')->firstOrFail();
        if ($res = $this->findOneBy( [
            'mer_user_id' => $this->userId(),
            'comment_id' => $commentId
        ])) {
            $res->delete();
            $comment->query()->where('id',$commentId)->decrement('like_count');
        }else{
            $comment->query()->where('id',$commentId)->increment('like_count');
            $res = $this->save( [
                'mer_user_id' => $this->userId(),
                'comment_id' => $commentId
            ]);
        }

        app(NoticeService::class)->publish($info['mer_user_id'],2,0,$commentId);

        return $res;
    }
}