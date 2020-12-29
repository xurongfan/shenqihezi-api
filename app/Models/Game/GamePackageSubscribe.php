<?php

namespace App\Models\Game;

use App\Base\Models\BaseModel;

class GamePackageSubscribe extends BaseModel
{
    protected $table = 'game_package_subscribe';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gamePackage()
    {
        return $this->hasOne(GamePackage::class,'id','game_package_id');
    }
}
