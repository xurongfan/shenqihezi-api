<?php

namespace App\Services\Channel;

use App\Base\Services\BaseService;

class ChannelGoogleService extends BaseService
{
    /**
     * 获取用户渠道配置
     * @return \App\Base\Services\BaseModel|string[]
     */
    public function userChannel()
    {
        if ($this->user()){
            $user = $this->user()->userInfo;
            if (isset($user['referrer']) && $user['referrer']) {
                $channel = $this->findOneBy([
                    'key' => $user['referrer']
                ],
                    'google_reward_key,google_interstitial_key,trad_plus_reward_key,trad_plus_interstitial_key'
                );
            }
        }

        return isset($channel) ? $channel : [
            'google_reward_key' => 'ca-app-pub-8909127857355685/9276771366',
            'google_interstitial_key' => 'ca-app-pub-8909127857355685/3166658589',
            'trad_plus_reward_key' => '79D5864CEB1073DF96D5566115298FB9',
            'trad_plus_interstitial_key' => '8AC10A52CFB0D784955C56A7F80EC56E',
        ];

    }
}