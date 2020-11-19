<?php

namespace App\Http\Controllers\MerUser;

use App\Base\Controllers\Controller;
use App\Services\MerUser\MerUserGameIntegralService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MerUserGameIntegralController extends Controller
{
    protected $service;

    /**
     * MerUserGameIntegralController constructor.
     * @param MerUserGameIntegralService $service
     */
    public function __construct(MerUserGameIntegralService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'game_package_id' => ['required', Rule::exists('game_package','id')],
            'integral' => 'required|integer|min:1',
        ]);
        return $this->service->integral($request->game_package_id,$request->integral);
    }

    /**
     * @param Request $request
     */
    public function rank(Request $request)
    {
        $this->validate($request,[
            'game_package_id' => ['required', Rule::exists('game_package','id')],
        ]);
        return $this->service->integralRank($request->game_package_id);
    }
}
