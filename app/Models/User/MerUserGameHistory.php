<?php

namespace App\Models\User;


use App\Base\Models\BaseModel;
use App\Models\Game\GamePackage;

class MerUserGameHistory extends BaseModel
{
    protected $table = 'mer_user_game_history';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gamePackage()
    {
        return $this->hasOne(GamePackage::class,'id','game_package_id');
    }
}
