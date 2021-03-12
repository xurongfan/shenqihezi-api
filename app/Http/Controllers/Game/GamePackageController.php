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

    /**
     * 查询游戏
     * @return mixed
     */
    public function show()
    {
        return app(GamePackageService::class)->show(\request()->input('game_package_id'));
    }

    /**
     * 根据标签/推荐筛选游戏
     * @param Request $request
     * @return mixed
     */
    public function gameIndexByTagRec(Request $request){
        return app(GamePackageService::class)->gameIndexByTagRec($request->game_tag_id,$request->is_rec,$request->title);
    }
}
