<?php

namespace App\Services\Topic;

use App\Base\Services\BaseService;

class TopicContentReportService extends BaseService
{
    /**
     * @param $topicId
     * @param $contentId
     * @param $content
     * @param $commentId
     * @return \App\Base\Services\BaseModel
     */
    public function store($topicId,$contentId,$commentId,$content)
    {
        if ($commentId) {
            $comment = app(TopicContentCommentService::class)->findOneBy([
                'id' => $commentId
            ]);
            $contentId = $comment['content_id'] ?? 0;
        }
        return $this->save([
            'mer_user_id' => $this->userId(),
            'topic_id' => $topicId,
            'content_id' => $contentId,
            'comment_id' => $commentId,
            'report_content' => $content,
        ]);
    }
}