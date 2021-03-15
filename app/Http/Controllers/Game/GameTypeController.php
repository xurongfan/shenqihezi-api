<?php


namespace App\Http\Controllers\Game;


use App\Base\Controllers\Controller;
use App\Services\Game\GameTypeService;

class GameTypeController extends Controller
{
    /**
     * GameTypeController constructor.
     * @param GameTypeService $service
     */
    public function __construct(GameTypeService $service)
    {
        $this->service = $service;
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->service->all();
    }
}