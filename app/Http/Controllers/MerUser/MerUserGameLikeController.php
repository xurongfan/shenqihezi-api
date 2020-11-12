<?php

namespace App\Http\Controllers\MerUser;

use App\Base\Controllers\Controller;
use App\Services\MerUser\MerUserGameLikeService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MerUserGameLikeController extends Controller
{
    protected $service;

    public function __construct(MerUserGameLikeService $service)
    {
        $this->service = $service;
    }

    /**
     * @return \App\Base\Services\BaseModel
     */
    public function like(Request $request)
    {
        $this->validate($request,[
            'game_package_id' => [
                'required' ,
                Rule::exists('game_package','id')
                ],
        ],[
            'game_package_id.required' => transL('game-package.game_package_id_empty_error'),
        ]);
        return $this->service->like(\request('game_package_id',0));
    }
}
