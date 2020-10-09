<?php


namespace App\Base\Traits;

use Predis\Client;

trait CacheTrait
{
    protected function getCacheClient()
    {
        return new Client(config('database.redis.default'));
    }

    /**
     * 缓存前缀
     * @return string
     */
    protected function getPrefix()
    {
        return config('cache.prefix') ? (config('cache.prefix') . ':') : '';
    }
    /**
     * 获取所有前缀为$key的所有key
     * @param $key
     * @return array
     */
    public function getCacheKeys($key)
    {
        return $this->getCacheClient()->keys($this->getPrefix() . $key . '*');
    }

    /**
     * 模糊清除所有前缀的缓存
     * @param $key
     */
    public function removeByKey($key)
    {
        $list = $this->getCacheKeys($key);
        foreach ($list as $item) {
            $this->getCacheClient()->del($item);
        }
    }
}
