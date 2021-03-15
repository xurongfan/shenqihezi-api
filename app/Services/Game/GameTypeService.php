<?php


namespace App\Services\Game;


use App\Base\Services\BaseService;
use App\Models\Game\GameType;

class GameTypeService extends BaseService
{
    /**
     * GameTypeService constructor.
     * @param GameType $model
     */
    public function __construct(GameType $model)
    {
        parent::__construct($model);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->newQuery()->select('id',getLangField('title').' as title')
            ->orderBy('sort','asc')->get();
    }
}