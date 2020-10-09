<?php

namespace App\Model\Video;

use App\Base\Models\BaseModel;

class SmallVideo extends BaseModel
{
    protected $table = 'small_videos';

    protected $connection = 'xiyou_mysql';
}
