<?php

namespace App\Models\User;


use App\Base\Models\BaseModel;
use App\Models\Game\GamePackage;

class MerUserGameIntegral extends BaseModel
{
    protected $table = 'mer_user_game_integral';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gamePackage()
    {
        return $this->hasOne(GamePackage::class,'id','game_package_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(MerUser::class,'id','mer_user_id');
    }
}
