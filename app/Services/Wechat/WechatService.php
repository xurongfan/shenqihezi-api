<?php


namespace App\Services\Wechat;


use Yansongda\Pay\Pay;

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
        $result = Pay::wechat($this->wechatServe())->app([
            'body' => $order['desc'],
            'out_trade_no' => $order['order_num'],
            'total_fee' => intval($order['amount'] * 100),
            'trade_type' => 'APP'
        ]);
        return $result->getContent();
    }

    /**
     * @param $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function notify($request)
    {
        $pay = Pay::wechat($this->wechatServe());

        try{
            $data = $pay->verify(); // 是的，验签就这么简单！
            logger('wechat notify:'.json_encode($data->all()));
        } catch (\Exception $e) {
             $e->getMessage();
        }

        return $pay->success();// laravel 框架中请直接 `return $pay->success()`
    }

    /**
     * @return \EasyWeChat\Payment\Application
     */
    public function wechatServe()
    {
        $config = [
            'appid'             => config('app.wechat_appid'),
            'mch_id'             => config('app.wechat_mch_id'),
            'key'                => config('app.wechat_pay_secret'),
            'notify_url'         => \route('wechat.notify'),
            'log' => [ // optional
                'file' => './logs/wechat.log',
                'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
                'type' => 'single', // optional, 可选 daily.
                'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
            ],
            'http' => [ // optional
                'timeout' => 5.0,
                'connect_timeout' => 5.0,
                // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
            ],
        ];
        return $config;
//        return \EasyWeChat\Factory::payment($config);
    }
}