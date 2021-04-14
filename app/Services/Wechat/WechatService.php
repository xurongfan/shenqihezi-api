<?php


namespace App\Services\Wechat;


use App\Models\Pay\PayOrder;
use App\Models\User\MerUser;
use App\Services\MerUser\MerUserCoinsLogService;
use App\Services\Pay\PayService;
use EasyWeChat\Factory;
use Yansongda\Pay\Pay;
use function AlibabaCloud\Client\json;

class WechatService
{
    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function auth($code)
    {
        $data = getHttpContent('GET', 'https://api.weixin.qq.com/sns/oauth2/access_token', [
            'appid' => config('app.wechat_appid'),
            'secret' => config('app.wechat_app_secret'),
            'code' => $code,
            'grant_type' => 'authorization_code'
        ]);
        $data = json_decode($data, true);
        if (isset($data['errcode'])) {
            throw new \Exception($data['errmsg'] ?? '');
        }
        if ($user = auth()->user()){
            $user->wechat_auth_code = $data['openid'];
            $user->save();
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
            'total_fee' => $order['amount'] * 100,
            'trade_type' => 'APP'
        ]);


        $result = $result->getContent();
        $result = json_decode($result,true);
        $result['order_num'] = $order['order_num'];
        logger('wechat order info:'.json_encode($result));
        return $result;
    }

    /**
     * @param $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function notify()
    {
        $pay = Pay::wechat($this->wechatServe());
        try {
            $data = $pay->verify();
            $data = $data->all();
//            $data = json_decode('            {
//                "appid":"wx9570383f3e10adb1",
//    "bank_type":"OTHERS",
//    "cash_fee":"1",
//    "fee_type":"CNY",
//    "is_subscribe":"N",
//    "mch_id":"1604486511",
//    "nonce_str":"WrkKUwrEMBECNeki",
//    "openid":"oPi2O6uGUt6T5EbRbE21_5laKoa0",
//    "out_trade_no":"2021012818460443690336",
//    "result_code":"SUCCESS",
//    "return_code":"SUCCESS",
//    "sign":"F06B1D5546430502AE7C1BE6297D4980",
//    "time_end":"20210128184612",
//    "total_fee":"1",
//    "trade_type":"APP",
//    "transaction_id":"4200000841202101287488834018"
//}',true);
            if (isset($data['result_code']) && $data['result_code'] == 'SUCCESS') {
               app(PayService::class)->notifyEvent($data['out_trade_no']);
            }

        } catch (\Exception $e) {
            $e->getMessage();
        }

        return $pay->success()->send();
    }

    /**
     * @return \EasyWeChat\Payment\Application
     */
    public function wechatServe()
    {
        $config = [
            'appid' => config('app.wechat_appid'),
            'mch_id' => config('app.wechat_mch_id'),
            'key' => config('app.wechat_pay_secret'),
            'notify_url' => \route('wechat.notify'),
//            'log' => [ // optional
//                'file' => './logs/wechat.log',
//                'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
//                'type' => 'single', // optional, 可选 daily.
//                'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
//            ],
            'http' => [ // optional
                'timeout' => 5.0,
                'connect_timeout' => 5.0,
            ],
        ];
        return $config;
//        return \EasyWeChat\Factory::payment($config);
    }

    public function withdraw()
    {
        $config = [
            // 必要配置
            'app_id'             => config('app.wechat_appid'),
            'mch_id'             => config('app.wechat_mch_id'),
            'key'                => config('app.wechat_pay_secret'),   // API 密钥

            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path'          => storage_path('cert/apiclient_cert.pem'), // XXX: 绝对路径！！！！
            'key_path'           => storage_path('cert/apiclient_key.pem'),      // XXX: 绝对路径！！！！
        ];

        $app = Factory::payment($config);

        $res = $app->transfer->toBalance([
            'partner_trade_no' => config('app.wechat_mch_id'), // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
            'openid' => 'oPi2O6uGUt6T5EbRbE21_5laKoa0',
            'check_name' => 'NO_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
            'amount' => 1, // 企业付款金额，单位为分
            'desc' => '理赔', // 企业付款操作说明信息。必填
        ]);
        echo"<pre>";print_r($res);exit;
    }
}