<?php

namespace App\Model\Push;

use App\Base\Models\BaseModel;

class PushArticle extends BaseModel
{
    protected $table = 'push_article';

    const TYPE_MAIN = '/main/home';
    const TYPE_WEBVIEW = '/main/webview';
    const TYPE_SMALL_VIDEO = '/video/small_video';
    const TYPE_VIDEO = '/video/detail';
    const TYPE_TASK_MAIN = '/task/home';
    const TYPE_KUAISHOU = '/video/kuaishou';

    public $type_arr = [self::TYPE_MAIN => '首页', self::TYPE_WEBVIEW => '网页链接', self::TYPE_SMALL_VIDEO => '小视频', self::TYPE_VIDEO => '视频页', self::TYPE_TASK_MAIN => '任务中心',self::TYPE_KUAISHOU=>'快手小视频'];
    public $push_channel_arr = ['YouMeng' => '友盟', 'HuaWei' => '华为', 'VIVO' => 'VIVO', 'OPPO' => 'OPPO', 'XiaoMi' => '小米'];
}
