<?php

namespace App\Models\Topic;


use App\Base\Models\BaseModel;
use App\Models\User\MerUser;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopicContentComment extends BaseModel
{
    use SoftDeletes;

    protected $table = 'topic_content_comment';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function content()
    {
        return $this->belongsTo(TopicContent::class,'content_id','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childComment()
    {
        return $this->hasMany(self::class,'pid','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parentComment()
    {
        return $this->hasOne(self::class,'id','pid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(MerUser::class,'id','mer_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function replyUser()
    {
        return $this->hasOne(MerUser::class,'id','reply_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function like()
    {
        return $this->hasOne(TopicContentCommentLike::class,'comment_id','id');
    }
}

