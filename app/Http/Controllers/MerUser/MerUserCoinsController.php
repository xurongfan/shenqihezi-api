<?php

namespace App\Http\Controllers\MerUser;

use App\Models\User\MerUserCoinsLog;
use App\Services\MerUser\MerUserCoinsLogService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class MerUserCoinsController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        return app(MerUserCoinsLogService::class)->index();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function obtain(Request $request)
    {
        $this->validate($request,[
            'type' => ['required',Rule::in(array_keys(MerUserCoinsLog::TYPE_COINS))]
        ]);
        return app(MerUserCoinsLogService::class)->obtain($request->type);
    }
}
