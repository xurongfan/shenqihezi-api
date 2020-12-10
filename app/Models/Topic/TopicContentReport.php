<?php

namespace App\Models\Topic;

use App\Base\Models\BaseModel;

class TopicContentReport extends BaseModel
{
    protected $table = 'topic_content_report';

    protected $casts = [
        'report_content' => 'array'
    ];
}
