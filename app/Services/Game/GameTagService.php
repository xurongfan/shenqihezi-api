<?php

namespace App\Services\Game;

use App\Base\Services\BaseService;

class GameTagService extends BaseService
{
    /**
     * @return mixed
     */
    public function all()
    {
        return $this->model->newQuery()->select('id',getLangField('title').' as title')
            ->orderBy('id','asc')->get();
    }
}