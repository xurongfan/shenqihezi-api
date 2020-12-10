<?php

namespace App\Services\Topic;

use App\Base\Services\BaseService;

class TopicContentReportService extends BaseService
{
    /**
     * @param $contentId
     * @param $content
     * @return \App\Base\Services\BaseModel
     */
    public function store($contentId,$content)
    {
        return $this->save([
            'mer_user_id' => $this->userId(),
            'content_id' => $contentId,
            'report_content' => $content,
        ]);
    }
}