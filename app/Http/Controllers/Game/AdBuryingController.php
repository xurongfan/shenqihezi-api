<?php

namespace App\Http\Controllers\Game;

use App\Services\Game\AdBuryingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdBuryingController extends \App\Base\Controllers\Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function report(Request $request)
    {
        $this->validate($request,[
            'package_id' => ['required', Rule::exists('game_package','id')],
            'type' => ['required',Rule::in([1, 2]),],
            'device_uid' => 'required',
            'show_type' => 'required',
        ]);
        return app(AdBuryingService::class)->report($request->package_id,$request->type,$request->device_uid,$request->show_type);
    }
}
