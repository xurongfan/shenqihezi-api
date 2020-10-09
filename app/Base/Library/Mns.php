<?php
/**
 * 阿里云的消息服务
 */

namespace App\Base\Library;

use AliyunMNS\Client;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Requests\PublishMessageRequest;

class Mns
{
    public $client;
    private $topicName = 'sbsp';

    public function __construct()
    {
        $this->client = new Client(
            config('app.MNS.MNS_ENDPOINT'),
            config('app.MNS.MNS_ACCESS_ID'),
            config('app.MNS.MNS_ACCESS_KEY')
        );
    }

    /**
     * 推送消息, 有订阅者会自动被调用
     * @param $topicName
     * @param $message
     * @return bool
     */
    private function pushMessage($topicName, $message)
    {
        $topic = $this->client->getTopicRef($topicName);
        $request = new PublishMessageRequest($message);
        try {
            $res = $topic->publishMessage($request);
            return $res->isSucceed();
        } catch (MnsException $e) {
            return false;
        }
    }

    /**
     * 自动化和评分
     * @param $message
     * @return bool
     */
    public function pushWorkflow($message)
    {
        $message['type'] = 'workflow';
        $res = $this->pushMessage($this->topicName, json_encode($message));
        return $res;
    }
}
