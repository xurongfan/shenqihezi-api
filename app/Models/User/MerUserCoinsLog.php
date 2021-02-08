<?php

namespace App\Models\User;

use App\Base\Models\BaseModel;

class MerUserCoinsLog extends BaseModel
{
    const TYPE_1 = 1;//绑定微信
    const TYPE_2 = 2;//时长奖励
    const TYPE_3 = 3;//抽奖
    const TYPE_4 = 4;//视频

    const TYPE_11 = 11;//提现

    const TYPE_COINS = [
        self::TYPE_1 => 60,//绑定微信奖励
        self::TYPE_2 => 40,
        self::TYPE_3 => 30,
        self::TYPE_4 => 100,
    ];

    const TYPE_COINS_TITLE = [
        self::TYPE_1 => '绑定微信',//绑定微信奖励
        self::TYPE_2 => '试玩游戏',
        self::TYPE_3 => '抽奖奖励',
        self::TYPE_4 => '视频奖励',
        self::TYPE_11 => '提现',
    ];

    protected $table = 'mer_user_coins_log';
}
