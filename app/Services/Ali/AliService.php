<?php


namespace App\Services\Ali;


use App\Services\Pay\PayService;
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

    /**
     * @param $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function notify($request)
    {
        logger('alipay data-1:'.json_encode($request));
        $config = config('alipay.pay');
        $config['notify_url'] = \route('alipay.notify');
        $config['format'] = 'json';
        $alipay = Pay::alipay($config);
        try{
            $data = $alipay->verify($request); // 是的，验签就这么简单！
            $data =  $data->all();
            if (isset($data['trade_status']) && $data['trade_status'] == 'TRADE_SUCCESS') {
                app(PayService::class)->notifyEvent($data['out_trade_no']);
            }
        } catch (\Exception $e) {
             throw new \Exception($e->getMessage());
        }

        return $alipay->success()->send();
    }

}