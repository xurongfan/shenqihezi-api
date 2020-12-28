<?php

namespace App\Models\User;

use App\Base\Models\BaseModel;

class MerUserFollow extends BaseModel
{
    protected $table = 'mer_user_follow';

    /**
     * 互相关注
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function follow()
    {
        return $this->hasOne(self::class,'follow_user_id','mer_user_id');
    }

    /**
     * 互相关注
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function followed()
    {
        return $this->hasOne(self::class,'mer_user_id','follow_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userInfo()
    {
        return $this->hasOne(MerUser::class,'id','mer_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function followUserInfo()
    {
        return $this->hasOne(MerUser::class,'id','follow_user_id');
    }
}
