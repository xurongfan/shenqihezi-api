<?php

namespace App\Http\Controllers\MerUser;

use App\Base\Controllers\Controller;
use App\Services\MerUser\MerUserGameHistoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MerUserGameHistoryController extends Controller
{
    protected $service;

    /**
     * MerUserGameHistoryController constructor.
     * @param MerUserGameHistoryService $service
     */
    public function __construct(MerUserGameHistoryService $service)
    {
        $this->service = $service;
    }

    /**
     * @return mixed
     */
    public function index(Request $request)
    {
//        $this->validate($request,[
//            'mer_user_id' => [ Rule::exists('mer_users','id')],
//        ]);
        return $this->service->index($request->mer_user_id);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'game_package_id' => ['required', Rule::exists('game_package','id')],
            'duration' => 'integer',
        ]);
        return $this->service->store($request->game_package_id,$request->duration);
    }

    /**
     * @param Request $request
     * @param $uid
     */
    public function report(Request $request,$uid)
    {
        $this->validate($request,[
            'game_package_id' => ['required', Rule::exists('game_package','id')],
            'duration' => 'required'
        ]);
        return $this->service->report($request->game_package_id,$uid,$request->duration);

    }

    /**
     * 热门游戏
     * @return array
     */
    public function hotGame()
    {
        return $this->service->hotGame();
    }

    /**
     * 置顶热门游戏
     * @return array
     */
    public function hotTopGame()
    {
        return $this->service->hotTopGame();
    }

}
