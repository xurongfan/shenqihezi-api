<?php

namespace App\Http\Controllers\Pay;

use App\Models\Pay\PayOrder;
use App\Services\Ali\AliService;
use App\Services\Pay\PayProjectService;
use App\Services\Pay\PayService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class PayController extends Controller
{
    /**
     * @return mixed
     */
    public function project(Request $request)
    {
        return app(PayProjectService::class)->index($request->is_google,$request->is_vip);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function pay(Request $request)
    {
        $this->validate($request,[
            'pay_type' => [
                'required',
                Rule::in([PayOrder::PAY_TYPE_WECHAT,PayOrder::PAY_TYPE_ALIPAY])
            ],
            'game_package_id' => [
                Rule::exists('game_package','id')
            ]
        ]);
        return app(PayService::class)->pay($request->pay_type,$request->project_id,$request->game_package_id);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function aliPayNotify(Request $request)
    {
        return app(AliService::class)->notify($request->all());
    }
}
