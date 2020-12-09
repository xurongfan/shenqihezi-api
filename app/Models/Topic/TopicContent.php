<?php

namespace App\Models\Topic;


use App\Base\Models\BaseModel;
use App\Models\User\MerUser;
use App\Models\User\MerUserFollow;

class TopicContent extends BaseModel
{
    const ISANONYMOUS_YES = 1;
    const ISANONYMOUS_NO = 0;

    protected $table = 'topic_content';

    protected $casts = [
        'image_resource' => 'array',
        'position_info' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topic()
    {
        return $this->belongsToMany(Topic::class,'topic_content_relation','content_id','topic_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comment()
    {
        return $this->hasMany(TopicContentComment::class,'content_id','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(MerUser::class,'id','mer_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userFollow()
    {
        return $this->hasMany(MerUserFollow::class,'follow_user_id','mer_user_id');
    }


}
