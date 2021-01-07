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
}