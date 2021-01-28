<?php

namespace App\Models\Pay;

use App\Base\Models\BaseModel;

class PayOrder extends BaseModel
{
    const PAY_TYPE_GOOGLE = 1;
    const PAY_TYPE_WECHAT = 2;
    const PAY_TYPE_ALIPAY = 3;

    const PAY_TYPE = [
        self::PAY_TYPE_GOOGLE,
        self::PAY_TYPE_WECHAT,
        self::PAY_TYPE_ALIPAY
    ];

    protected $table = 'pay_order';
}
