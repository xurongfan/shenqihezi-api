<?php

namespace App\Models\User;

use App\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class MerUserLoginLog extends BaseModel
{
    protected $table = 'mer_user_login_log';

    public $timestamps = false;


}
