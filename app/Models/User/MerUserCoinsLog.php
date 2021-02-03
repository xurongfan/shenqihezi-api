<?php

namespace App\Models\User;

use App\Base\Models\BaseModel;

class MerUserCoinsLog extends BaseModel
{
    const TYPE_1 = 1;//绑定微信
    const TYPE_2 = 2;//时长奖励
    const TYPE_3 = 3;//抽奖
    const TYPE_4 = 4;//视频

    const TYPE_5 = 5;//提现

    protected $table = 'mer_user_coins_log';
}
