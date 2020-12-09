<?php

namespace App\Models\Notice;

use App\Base\Models\BaseModel;
use App\Models\Topic\TopicContent;
use App\Models\Topic\TopicContentComment;
use App\Models\User\MerUser;

class Notice extends BaseModel
{
    protected $table = 'notice';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function content()
    {
        return $this->hasOne(TopicContent::class,'id','content_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function comment()
    {
        return $this->hasOne(TopicContentComment::class,'id','comment_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(MerUser::class,'id','originate_user_id');
    }
}
