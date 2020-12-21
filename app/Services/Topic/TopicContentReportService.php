<?php

namespace App\Services\Topic;

use App\Base\Services\BaseService;

class TopicContentReportService extends BaseService
{
    /**
     * @param $contentId
     * @param $content
     * @param $commentId
     * @return \App\Base\Services\BaseModel
     */
    public function store($contentId,$commentId,$content)
    {
        return $this->save([
            'mer_user_id' => $this->userId(),
            'content_id' => $contentId,
            'comment_id' => $commentId,
            'report_content' => $content,
        ]);
    }
}