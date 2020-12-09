<?php

namespace App\Models\Topic;


use App\Base\Models\BaseModel;

class Topic extends BaseModel
{
    protected $table = 'topic';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topicContent()
    {
        return $this->hasMany(TopicContentRelation::class,'topic_id','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topicUser()
    {
        return $this->hasMany(TopicUser::class,'topic_id','id');
    }
}
