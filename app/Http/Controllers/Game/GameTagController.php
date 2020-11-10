<?php

namespace App\Http\Controllers\Game;

use App\Services\Game\GameTagService;
use Illuminate\Http\Request;

class GameTagController extends \App\Base\Controllers\Controller
{
    /**
     * GameTagController constructor.
     * @param GameTagService $service
     */
    public function __construct(GameTagService $service)
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
