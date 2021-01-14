<?php


namespace App\Services\Ali;


class AliService
{
    /**
     * @return false|string
     */
    public function pay()
    {
        $aliPayOrder = [
            'out_trade_no' => time(),
            'total_amount' => 0.01, // 支付金额
            'subject'      => $request->subject ?? '默认' // 备注
        ];

        $config = config('alipay.pay');

        $config['return_url'] = $config['return_url'].'?id=1';
        $data = \Yansongda\Pay\Pay::alipay($config)->app($aliPayOrder);
        return $data->getContent();
    }

    public function notify()
    {
        $order = Order::find($request->id);
        // 更新自己项目 订单状态
        if(!empty($order))  $orderService->payOrder($order);

    }
}