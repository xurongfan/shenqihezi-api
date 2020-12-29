<?php

namespace App\Http\Controllers\Game;

use App\Base\Controllers\Controller;
use App\Services\Game\GamePackageService;
use App\Services\Game\GamePackageSubscribeService;
use Illuminate\Http\Request;

class GamePackageController extends Controller
{
    /**
     * 游戏列表
     * @return mixed
     */
    public function index()
    {
        return app(GamePackageService::class)->index();
    }

    /**
     * 游戏订阅列表
     * @return mixed
     */
    public function subscribe()
    {
        return app(GamePackageSubscribeService::class)->index();
    }
}
