<?php

namespace App\Models\User;

use App\Models\Game\GameTag;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicUser;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Base\Models\BaseModel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class MerUser extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    JWTSubject
{
    use Authenticatable, Authorizable, CanResetPassword;

    protected $table = 'mer_users';

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(GameTag::class, 'mer_user_tag', 'user_id', 'tag_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tagsId()
    {
        return $this->hasMany(MerUserTag::class,'user_id','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topic()
    {
        return $this->hasMany(TopicUser::class,'mer_user_id');
    }

    /**
     * 粉丝数
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function follow()
    {
        return $this->hasMany(MerUserFollow::class,'follow_user_id','id');
    }

    /**
     * 是否关注
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function isUserFollow()
    {
        return $this->hasOne(MerUserFollow::class,'follow_user_id','id');
    }

    /**
     * 关注数
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function followed()
    {
        return $this->hasMany(MerUserFollow::class,'mer_user_id','id');
    }

    /**
     * 用户信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userInfo()
    {
        return $this->hasOne(MerUserInfo::class,'mer_user_id','id');
    }
}
