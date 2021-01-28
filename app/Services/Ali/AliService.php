<?php


namespace App\Services\Ali;


class AliService
{
    /**
     * @param $order
     * @return false|string
     */
    public function pay($order)
    {
        $aliPayOrder = [
            'out_trade_no' => $order['order_num'],
            'total_amount' => $order['amount'], // 支付金额
            'subject'      => $order['desc']
        ];

        $config = config('alipay.pay');
        $config['notify_url'] = \route('alipay.notify');
        $data = \Yansongda\Pay\Pay::alipay($config)->app($aliPayOrder);
        return $data->getContent();
    }

    public function notify($request)
    {
        logger('alipay notify:'.json_encode($request));
        return true;

        $order = Order::find($request->id);
        // 更新自己项目 订单状态
        if(!empty($order))  $orderService->payOrder($order);

    }
}