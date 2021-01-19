<?php

namespace App\Models\System;

use App\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class SysFeedBack extends BaseModel
{
    protected $table = 'sys_feedback';

    protected $casts = [
        'imgs' => 'array'
    ];
}
