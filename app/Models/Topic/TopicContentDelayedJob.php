<?php

namespace App\Models\Topic;

use App\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class TopicContentDelayedJob extends BaseModel
{
    protected $table = 'topic_content_delayed_job';

    public $timestamps = false;

    protected $casts = [
        'extra_info' => 'array'
    ];
}
