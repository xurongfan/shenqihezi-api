<?php

namespace App\Http\Controllers\Wechat;

use App\Services\Wechat\WechatService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WechatController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function auth(Request $request)
    {
        $this->validate($request,[
            'code' => 'required'
        ]);
        return app(WechatService::class)->auth($request->code);
    }

    /**
     * @return mixed
     */
    public function notify(Request $request)
    {
        return app(WechatService::class)->notify($request->all());
    }
}
