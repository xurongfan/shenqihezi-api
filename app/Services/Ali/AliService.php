<?php


namespace App\Services\Ali;


use Yansongda\Pay\Pay;

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
        $config['format'] = 'json';
        $result = \Yansongda\Pay\Pay::alipay($config)->app($aliPayOrder);

        return [
            'sign' => $result->getContent(),
            'order_num' => $order['order_num']
        ];
    }

    public function notify($request)
    {
        logger('alipay notify-1:'.json_encode($request));
        $config = config('alipay.pay');
        $config['notify_url'] = \route('alipay.notify');
        $config['format'] = 'json';
        $alipay = Pay::alipay($config);
        try{
            $data = $alipay->verify(); // 是的，验签就这么简单！
            $data =  $data->all();
           logger('alipay notify-2:'.json_encode($data));

        } catch (\Exception $e) {
             throw new \Exception($e->getMessage());
        }

        return $alipay->success();
    }

}