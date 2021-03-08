<?php

namespace App\Services\Statics;

use App\Base\Services\BaseService;
use App\Models\Statics\StaticsRemain;

class StaticsRemainService extends BaseService
{
    public function __construct(StaticsRemain $model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        
    }
}