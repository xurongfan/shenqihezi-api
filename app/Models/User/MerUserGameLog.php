<?php

namespace App\Models\User;

use App\Base\Models\BaseModel;
use App\Models\Game\GamePackage;
use Illuminate\Database\Eloquent\Model;

class MerUserGameLog extends BaseModel
{
    protected $table = 'mer_user_game_log';

    public function gamePackage()
    {
        return $this->hasOne(GamePackage::class,'id','game_package_id');
    }
}
