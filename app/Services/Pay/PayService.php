<?php


namespace App\Services\Pay;


use App\Models\Pay\PayOrder;
use App\Services\Ali\AliService;
use App\Services\Wechat\WechatService;

class PayService
{
    /**
     * @param int $payType
     * @param int $projectId
     * @param int $gamePackageId
     * @return mixed
     * @throws \Exception
     */
    public function pay($payType = 0,$projectId = 0,$gamePackageId = 0)
    {
        $project = app(PayProjectService::class)->findOneBy(
            [
                'google_pay_id' => '',
                'id' => $projectId
            ]
        );
       if (empty($project)) {
           throw new \Exception(transL('common.pay_project_error'));
       }
       //生成订单信息
        $order = PayOrder::query()->create([
            'mer_user_id' => auth()->user()->id,
            'order_num' => makeOrderNumber(),
            'currency_code' => 'cny',
            'amount' => $project['amount'],
            'pay_type' => $payType,
            'good_type' => $project['is_vip'] ? 1 :2,
            'game_package_id' => $gamePackageId ? $gamePackageId : 0,
            'desc' => $project['title']
        ]);

        $result = $payType == PayOrder::PAY_TYPE_WECHAT ?
            app(WechatService::class)->pay($order)
            :
            app(AliService::class)->pay($order);

        return $result;
    }
}