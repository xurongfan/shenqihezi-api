<?php

namespace App\Models\Game;

use App\Base\Models\BaseModel;
use App\Models\User\MerUser;

class GameTag extends BaseModel
{
    protected $table = 'game_tags';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(MerUser::class,'mer_user_tag','tag_id','user_id');
    }
}
