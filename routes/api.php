<?php

use App\Services\Topic\TopicService;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Ramsey\Uuid\Uuid;
use ReceiptValidator\iTunes\Validator as iTunesValidator;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::options('/{all}', function() {
//    return response('options here!');
//})->where(['all' => '([a-zA-Z0-9-]|/)+']);


Route::group([],function (Router $router){

    $router->group(['namespace' => 'MerUser','prefix' => 'user'],function ($router){
        $router->post('reg', 'MerUserController@reg')->name('user.reg');
        $router->post('sendSms', 'MerUserController@sendSms')->name('user.sms');
        $router->any('login', 'MerUserController@login')->name('user.login');
    });

    $router->group(['namespace' => 'Game','prefix' => 'game'],function ($router){
        $router->get('tag', 'GameTagController@all')->name('game.tag');
    });

    $router->group(['namespace' => 'System','prefix' => 'system'],function ($router){
        $router->get('config', 'SysConfigController@config')->name('system.config');
    });

    $router->group(['namespace' => 'Wechat','prefix' => 'wechat'],function ($router){
        $router->get('auth', 'WechatController@auth')->name('wechat.auth');
        $router->any('notify', 'WechatController@notify')->name('wechat.notify');
    });

});

Route::group(['middleware' => 'auth_token'],function (Router $router){

    $router->group(['namespace' => 'MerUser','prefix' => 'user'],function ($router){
        $router->get('info', 'MerUserController@info')->name('user.info');
        $router->post('out', 'MerUserController@out')->name('user.out');
        $router->put('edit', 'MerUserController@edit')->name('user.edit');
        $router->put('info', 'MerUserController@editInfo')->name('user.edit-info');
        $router->get('/info/{id}', 'MerUserController@user')->name('user.show');
        $router->post('game-like', 'MerUserGameLikeController@like')->name('user.game-like');

        $router->post('pay', 'MerUserController@pay')->name('user.pay');

        $router->post('game-collect', 'MerUserGameCollectionController@collect')->name('user.game-collect');
        $router->get('game-collect', 'MerUserGameCollectionController@index')->name('user.game-collect-index');

        $router->get('game-history', 'MerUserGameHistoryController@index')->name('user.game-history-index');
        $router->post('game-history', 'MerUserGameHistoryController@store')->name('user.game-history-store');
        $router->put('game-history/{uid}', 'MerUserGameHistoryController@report')->name('user.game-history-report');
        $router->get('game-hot', 'MerUserGameHistoryController@hotGame')->name('user.game-hot');

        $router->post('game-integral', 'MerUserGameIntegralController@store')->middleware('LaraRsa')->name('user.game-integral');
        $router->get('game-integral-rank', 'MerUserGameIntegralController@rank')->name('user.game-integral-rank');

        $router->post('follow', 'MerUserFollowController@follow')->name('user.follow');
        $router->get('fans', 'MerUserFollowController@index')->name('user.fans');
        $router->get('my-follow', 'MerUserFollowController@myFollow')->name('user.my-follow');


    });

    $router->group(['namespace' => 'Game','prefix' => 'game'],function ($router){
        $router->get('/', 'GamePackageController@index')->name('game.index');
        $router->post('report', 'AdBuryingController@report')->name('game-ad.report');
        $router->get('subscribe', 'GamePackageController@subscribe')->name('game.subscribe');
    });

    $router->group(['namespace' => 'Topic','prefix' => 'topic'],function ($router){
        $router->get('search', 'TopicController@search')->name('topic.search');
        $router->get('/', 'TopicController@index')->name('topic.index');
        $router->get('user-topic-list', 'TopicController@userTopicList')->name('topic.user.topic');
        $router->post('/', 'TopicController@follow')->name('topic.follow');

        $router->post('content', 'TopicContentController@publish')->name('topic.content.publish');
        $router->post('cancel-anonymous', 'TopicContentController@cancelAnonymous')->name('topic.content.anonymous');

        $router->get('my-topic-content', 'TopicContentController@myTopicContent')->name('topic.my-topic-content');

        $router->delete('content', 'TopicContentController@delete')->name('topic.content.delete');

        $router->post('comment', 'TopicContentController@comment')->name('topic.comment.publish');
        $router->get('comment', 'TopicContentController@commentList')->name('topic.comment.list');
        $router->delete('comment', 'TopicContentController@deleteComment')->name('topic.comment.delete');

        $router->post('comment-like', 'TopicContentController@commentLike')->name('topic.comment.like');
        $router->post('content-like', 'TopicContentController@like')->name('topic.content.like');

        $router->get('content', 'TopicContentController@index')->name('topic.content.list');
        $router->get('content/{contentId}', 'TopicContentController@show')->name('topic.content.show');


        $router->get('notice', 'NoticeController@index')->name('topic.notice.list');
        $router->get('notice-count', 'NoticeController@noticeCount')->name('topic.notice.count');
        $router->get('my-comment', 'TopicContentController@myComment')->name('topic.comment.my-comment');


        $router->post('report', 'TopicContentController@report')->name('topic.content.report');

        $router->post('shield', 'TopicContentController@shield')->name('topic.content.shield');

    });

    $router->group(['namespace' => 'Channel','prefix' => 'channel'],function ($router){
        $router->get('/user', 'ChannelController@userChannel')->name('channel.user');
    });


    $router->group(['namespace' => 'Tool','prefix' => 'tool'],function ($router){

        $router->post('upload', 'CommonController@upload')->name('tool.upload');
    });

    $router->group(['namespace' => 'System','prefix' => 'system'],function ($router){
        $router->post('feedback', 'SysFeedBackController@store')->name('feedback.store');
    });




});

Route::any('/captcha', function (){
    return [
        'url' => app('captcha')->create('default', true)
    ];
})->name('captcha');


Route::any('game/list', function (){
    $list = \App\Models\Game\GamePackage::query()->orderBy('id','desc')->paginate(20);
    $list = $list ? $list->toArray() : [];

    foreach ($list['data'] as $k => &$v){
        $v['icon_img'] = config('filesystems.disks.oss.domain_url').$v['icon_img'];
        $v['background_img'] = config('filesystems.disks.oss.domain_url').$v['background_img'];
        $gameUrl = env('GAME_URL');
        $v['url'] = $v['url'] ? $gameUrl.$v['url'] : '';
        $v['crack_url'] = $v['crack_url']  ? env('CRACK_GAME_URL').$v['crack_url'] : '';
    }
    return $list;
})->name('game-list');

Route::any('ad-game/list', function (){

    return [
        [
            'img' => 'https://android-artworks.25pp.com/fs08/2018/02/06/4/106_508240d7599857dec1b1efd5534464ed_con_130x130.png',
            'dec' => '史上最强的开心消消乐熊出没消除游戏消灭星星',
            'title' => '消灭星星',
            'url' => 'https://www.wandoujia.com/apps/6623588/binding?source=web_seo_baidu_binded',
        ],
        [
            'img' => 'https://android-artworks.25pp.com/fs08/2020/06/01/7/1_64d55e77e2564eb274a55ab64b49b521_con_130x130.png',
            'dec' => '开心消消乐」姐妹篇，全新玩法，快乐升级',
            'title' => '海滨消消乐',
            'url' => 'https://www.wandoujia.com/apps/7589903/binding?source=web_seo_baidu_binded',
        ],
        [
            'img' => 'https://android-artworks.25pp.com/fs08/2020/10/14/4/110_38d99afc681a81f7f147e41b96e9935a_con_130x130.png',
            'dec' => '不仅是加速器更是黑科技，内含大量海外游戏，终于能愉快玩外服了。',
            'title' => 'biubiu加速器',
            'url' => 'https://www.wandoujia.com/apps/7854150/binding?source=web_seo_baidu_binded',
        ],
        [
            'img' => 'https://android-artworks.25pp.com/fs08/2019/05/08/10/1_e46c4b93938451b2be8630e7a4dea8e8_con_130x130.png',
            'dec' => '腾讯光子打造的反恐军事竞赛手游',
            'title' => '和平精英',
            'url' => 'https://www.wandoujia.com/apps/7701857/binding?source=web_seo_baidu_binded',
        ],
        [
            'img' => 'https://android-artworks.25pp.com/fs08/2020/06/09/6/109_69cec412575af9cc2d0e60efd8d27924_con_130x130.png',
            'dec' => '大球吃小球，努力成为巨无霸，在游戏里处处都能学到套路。',
            'title' => '球球大作战',
            'url' => 'https://www.wandoujia.com/apps/6618368/binding?source=web_seo_baidu_binded',
        ]
    ];
})->name('ad-game-list');

Route::any('/test', function () {
    $iClientProfile = \AlibabaCloud\Client\Profile\DefaultProfile::getProfile("cn-shanghai",'LTAI4GAeD3jcsVmvedfNw922', 'HK3f7xu1gJlo4beVqSE3ygYiEF9qmG'); // TODO
    $client = new \AlibabaCloud\Client\DefaultAcsClient($iClientProfile);

    $request = new \AlibabaCloud\Green\V20180509\ImageSyncScan();

    $request->setMethod("POST");
    $request->setAcceptFormat("JSON");

    $task1 = array('dataId' =>  uniqid(),
        'url' => 'https://fun-touch.oss-cn-shanghai.aliyuncs.com/game-images/20201229/7GAeoObnPPThWzxeyvmUlQEr6tPDilv3br306zgw.jpeg?x-oss-process=style/yasuo',
        'time' => round(microtime(true)*1000)
    );
    $request->setContent(json_encode(array("tasks" => array($task1),
        "scenes" => array("porn","terrorism",'ad','live'))));
    try {
        $response = $client->getAcsResponse($request);
        echo"<pre>";print_r($response->data);exit;

        if(200 == $response->code){
            $taskResults = $response->data;

            foreach ($taskResults as $taskResult) {
                if(200 == $taskResult->code){
                    $taskId = $taskResult->taskId;
                    print_r($taskId);
                    // 将taskId 保存下来，间隔一段时间来轮询结果, 参照ImageAsyncScanResultsRequest
                }else{
                    print_r("task process fail:" + $response->code);
                }
            }
        }else{
            print_r("detect not success. code:" + $response->code);
        }
    } catch (Exception $e) {
        print_r($e);
    }
    exit();

    $googleClient = new \Google_Client();
//    $client->useApplicationDefaultCredentials();
//    $client->setAuthConfig($config_path);
//    $client->setScopes(['https://www.googleapis.com/auth/firebase.messaging']);     # 授予访问 FCM 的权限
//    return $client->fetchAccessTokenWithAssertion();
    $googleClient->setScopes([\Google_Service_AndroidPublisher::ANDROIDPUBLISHER]);
    $googleClient->setApplicationName('FouTouch');
    $googleClient->setAuthConfig(public_path('FunTouch-6a5c57d1ce4e.json'));

    $googleAndroidPublisher = new \Google_Service_AndroidPublisher($googleClient);
    $validator = new \ReceiptValidator\GooglePlay\Validator($googleAndroidPublisher);

    try {
        $user = auth()->user();
        $response = $validator->setPackageName('com.magic.taper')
            ->setProductId('p2')
            ->setPurchaseToken('hfahodmhdliodbcbcmapdibm.AO-J1OzJmygXZCwxJO7YuxB21FILQrRFvGbpAJke80MCEmxDgnOuJmZbRO7gC-Bemj4ltTfwrJSk8XSZ2vS8xXzx1y-GIMDz8A')
            ->validatePurchase();
        //已付款
        echo"<pre>";print_r($response);exit;
    } catch (\Exception $e){
        throw new \Exception($e->getMessage());
    }
    exit();
    // 阿里云主账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM账号进行API访问或日常运维，请登录RAM控制台创建RAM账号。
    $accessKeyId = "LTAI4GAeD3jcsVmvedfNw922";
    $accessKeySecret = "HK3f7xu1gJlo4beVqSE3ygYiEF9qmG";
// Endpoint以杭州为例，其它Region请按实际情况填写。
    $endpoint = "http://oss-cn-hangzhou.aliyuncs.com";
    $bucket= "cn-funtouch";
    $object = "germ-squirmish.zip";

// 设置URL的有效时长为3600s。
    $timeout = 300;
    try {
        $ossClient = new \OSS\OssClient($accessKeyId, $accessKeySecret, $endpoint, false);

        // 生成GetObject的签名URL。
        $signedUrl = $ossClient->signUrl($bucket, $object, $timeout);
    } catch (\OSS\Core\OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": signedUrl: " . $signedUrl . "\n");
    exit();

});

Route::any('/topic-content', function () {
    set_time_limit(0);
    $file = request()->input('file');
    $data = file_get_contents(storage_path('content/'.$file));
    $data = json_decode($data,true);
//    echo"<pre>";print_r(count($data['data']));exit;
    $count = 0;
    foreach ($data['data'] as $k => $datum){
        if (isset($datum['pic_urls']) && $datum['pic_urls']) {
            foreach ($datum['pic_urls'] as &$v){
                $v =  importImage('pic/'.$v.'.png');
            }
        }
        $user = \App\Models\User\MerUser::query()->firstOrCreate([
            'phone' => $datum['uid']
        ],[
            'nick_name' => $datum['username'],
            'profile_img' => importImage('avatar/'.$datum['avatar_url'].'.png'),
            'sex' => $datum['gender'] == 1 ? 'male' : 'female',
            'birth' => $datum['birthday'],
        ]);

        $content = \App\Models\Topic\TopicContent::query()->create([
            'mer_user_id' => $user->id,
            'content' => $datum['content'],
            'image_resource' => $datum['pic_urls'],
            'created_at' => date('Y-m-d H:i:s',$datum['timestamp']),
            'updated_at' => date('Y-m-d H:i:s',$datum['timestamp'])
        ]);

        $topicService = app(TopicService::class);
        if (isset($datum['topicnames']) && $datum['topicnames']) {
            $topicArr = [];
//            foreach ($datum['topicnames'] as $key => $value) {
//                $topicArr[] = $topicService->findOrCreate($value,$user->id)->id;
//            }
            $topicArr[] = $topicService->findOrCreate($datum['topicnames'],$user->id)->id;
            $topicArr && $content->topic()->sync($topicArr);
        }

        //游戏历史
        $gameList = \App\Models\Game\GamePackage::query()
            ->select('id')
            ->where('id','>=',rand(8,160))
            ->where('status','=',1)
            ->limit(5)
            ->get()
            ->toArray();

        foreach ($gameList as $game){
            \App\Models\User\MerUserGameHistory::query()->create([
                'uid' => Uuid::uuid1()->toString(),
                'mer_user_id' => $user->id,
                'game_package_id' => $game['id']
            ]);
        }

        //游戏虚拟积分
        $gameRankList = \App\Models\Game\GamePackage::query()
            ->select('id')
            ->where('id','>=',rand(8,160))
            ->where('is_rank',1)
            ->where('status','=',1)
            ->limit(5)
            ->get()
            ->toArray();

        foreach ($gameRankList as $game){
            \App\Models\User\MerUserGameIntegral::query()->updateOrCreate([
                'mer_user_id' => $user->id,
                'game_package_id' => $game['id']
            ],[
                'integral' => rand(10,50),
                'mer_user_id' => $user->id,
                'game_package_id' => $game['id']
            ]);
        }
        $count++;
    }
    echo '导入'.$count.'条数据';
});


