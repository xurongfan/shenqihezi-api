<?php


namespace App\Services\Wechat;


class WechatService
{
    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function auth($code)
    {
        $data = getHttpContent('GET','https://api.weixin.qq.com/sns/oauth2/access_token',[
            'appid' => config('app.wechat_appid'),
            'secret' => config('app.wechat_app_secret'),
            'code' => $code,
            'grant_type' => 'authorization_code'
        ]);
        $data = json_decode($data,true);
        if (isset($data['errcode'])) {
            throw new \Exception($data['errmsg'] ?? '');
        }
        return $data;
    }

    /**
     * @param $order
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pay($order)
    {
        return $this->wechatServe()->order->unify([
            'body' => $order['desc'],
            'out_trade_no' => $order['order_num'],
            'total_fee' => intval($order['amount'] * 100),
            'trade_type' => 'APP'
        ]);
    }

    /**
     * @param $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function notify($request)
    {
        logger('wechat notify:'.json_encode($request));
        $response = $this->wechatServe()->handlePaidNotify(function($message, $fail){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = 查询订单($message['out_trade_no']);

            if (!$order || $order->paid_at) { // 如果订单不存在 或者 订单已经支付过了
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if (array_get($message, 'result_code') === 'SUCCESS') {
                    $order->paid_at = time(); // 更新支付时间为当前时间
                    $order->status = 'paid';

                    // 用户支付失败
                } elseif (array_get($message, 'result_code') === 'FAIL') {
                    $order->status = 'paid_fail';
                }
            } else {
                throw new \Exception('通信失败，请稍后再通知我');
            }

            $order->save();

            return true;
        });

        return $response;
    }

    /**
     * @return \EasyWeChat\Payment\Application
     */
    public function wechatServe()
    {
        $config = [
            'app_id'             => config('app.wechat_appid'),
            'mch_id'             => config('app.wechat_mch_id'),
            'key'                => config('app.wechat_pay_secret'),
            'notify_url'         => \route('wechat.notify')
        ];
        return \EasyWeChat\Factory::payment($config);
    }
}