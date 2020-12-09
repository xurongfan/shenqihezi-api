<?php

namespace App\Models\Topic;


use App\Base\Models\BaseModel;

class TopicUser extends BaseModel
{
    protected $table = 'topic_user';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class,'topic_id','id');
    }
}
