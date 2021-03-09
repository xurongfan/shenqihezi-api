<?php

namespace App\Http\Controllers\MerUser;

use App\Base\Controllers\Controller;
use App\Services\MerUser\MerUserGameCollectionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MerUserGameCollectionController extends Controller
{
    protected $service;

    public function __construct(MerUserGameCollectionService $service)
    {
        $this->service = $service;
    }

    /**
     * 收藏
     * @return \App\Base\Services\BaseModel
     */
    public function collect(Request $request)
    {
        $this->validate($request,[
            'game_package_id' => [
                'required' ,
                Rule::exists('game_package','id')
            ],
        ],[
            'game_package_id.required' => transL('game-package.game_package_id_empty_error'),
        ]);
        return $this->service->collect(\request('game_package_id',0));
    }

    /**
     * 我的收藏
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        return $this->service->index();
    }

    /**
     * @param Request $request
     * @return \App\Base\Services\BaseModel
     */
    public function gameCollect(Request $request)
    {
        $this->validate($request,[
            'game_package_id' => [
                'required' ,
                Rule::exists('game_package','id')
            ],
        ],[
            'game_package_id.required' => transL('game-package.game_package_id_empty_error'),
        ]);

        return $this->service->gameCollect(\request('game_package_id',0));
    }
}
