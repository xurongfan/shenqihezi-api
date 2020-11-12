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
    public function index()
    {
        return $this->service->index();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'game_package_id' => ['required', Rule::exists('game_package','id')]
        ]);
        return $this->service->store($request->game_package_id);
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

}
