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
            'total_fee' => intval($order['amount'] * 100),
            'trade_type' => 'APP'
        ]);
        $result = $result->getContent();
        $result = json_decode($result,true);
        $result['order_num'] = $order['order_num'];
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
//                'level' => 'info', // ????????????????????????????????? info?????????????????? debug
//                'type' => 'single', // optional, ?????? daily.
//                'max_file' => 30, // optional, ??? type ??? daily ?????????????????? 30 ???
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
            // ????????????
            'app_id'             => config('app.wechat_appid'),
            'mch_id'             => config('app.wechat_mch_id'),
            'key'                => config('app.wechat_pay_secret'),   // API ??????

            // ????????????????????????????????????????????????????????????????????? API ????????????(???????????????????????? API ??????)
            'cert_path'          => storage_path('cert/apiclient_cert.pem'), // XXX: ????????????????????????
            'key_path'           => storage_path('cert/apiclient_key.pem'),      // XXX: ????????????????????????
        ];

        $app = Factory::payment($config);

        $res = $app->transfer->toBalance([
            'partner_trade_no' => config('app.wechat_mch_id'), // ????????????????????????????????????(???????????????????????????????????????????????????)
            'openid' => 'oPi2O6uGUt6T5EbRbE21_5laKoa0',
            'check_name' => 'NO_CHECK', // NO_CHECK????????????????????????, FORCE_CHECK????????????????????????
            'amount' => 1, // ?????????????????????????????????
            'desc' => '??????', // ???????????????????????????????????????
        ]);
        echo"<pre>";print_r($res);exit;
    }
}