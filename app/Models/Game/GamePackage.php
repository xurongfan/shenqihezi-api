<?php

namespace App\Models\Game;


use App\Base\Models\BaseModel;

class GamePackage extends BaseModel
{
    protected $table = 'game_package';

    /**
     * 是否订阅
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function subscribe()
    {
        return $this->hasOne(GamePackageSubscribe::class,'game_package_id','id');
    }
}
