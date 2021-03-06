<?php

use App\Models\Message\MessageFcm;
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


Route::group([], function (Router $router) {

    $router->group(['namespace' => 'MerUser', 'prefix' => 'user'], function ($router) {
        $router->post('reg', 'MerUserController@reg')->name('user.reg');
        $router->post('sendSms', 'MerUserController@sendSms')->name('user.sms');
        $router->post('login', 'MerUserController@login')->name('user.login');
        $router->post('newLogin', 'MerUserController@newLogin')->name('user.newLogin');
        $router->get('/info/{id}', 'MerUserController@user')->name('user.show');

        $router->get('game-history', 'MerUserGameHistoryController@index')->name('user.game-history-index');
        $router->post('game-history', 'MerUserGameHistoryController@store')->name('user.game-history-store');
        $router->put('game-history/{uid}', 'MerUserGameHistoryController@report')->name('user.game-history-report');
        $router->get('game-hot', 'MerUserGameHistoryController@hotGame')->name('user.game-hot');
        $router->get('game-hot-top', 'MerUserGameHistoryController@hotTopGame')->name('user.game-hot-top');

        $router->get('game-integral-rank', 'MerUserGameIntegralController@rank')->name('user.game-integral-rank');


    });

    $router->group(['namespace' => 'Game', 'prefix' => 'game'], function ($router) {
        $router->get('tag', 'GameTagController@all')->name('game.tag');
        $router->get('type', 'GameTypeController@all')->name('game.type');
        $router->get('gameIndexByTagRec', 'GamePackageController@gameIndexByTagRec')->name('game.gameIndexByTagRec');
        $router->get('gameRec', 'GamePackageController@gameRec')->name('game.gameRec');
        $router->get('show', 'GamePackageController@show')->name('game.show');
        $router->post('report', 'AdBuryingController@report')->name('game-ad.report');

    });

    $router->group(['namespace' => 'Topic', 'prefix' => 'topic'], function ($router) {
        $router->get('/', 'TopicController@index')->name('topic.index');
        $router->get('game-topic', 'TopicContentController@gameTopic')->name('topic.game-topic');
        $router->get('content', 'TopicContentController@index')->name('topic.content.list');
        $router->get('comment', 'TopicContentController@commentList')->name('topic.comment.list');
        $router->get('content/{contentId}', 'TopicContentController@show')->name('topic.content.show');

    });

    $router->group(['namespace' => 'Channel', 'prefix' => 'channel'], function ($router) {
        $router->get('/user', 'ChannelController@userChannel')->name('channel.user');
    });

    $router->group(['namespace' => 'Tool', 'prefix' => 'tool'], function ($router) {
        $router->post('getIpAddress', 'CommonController@getIpAddress');
    });

    $router->group(['namespace' => 'System', 'prefix' => 'system'], function ($router) {
        $router->post('feedback', 'SysFeedBackController@store')->name('feedback.store');
        $router->post('client', 'SysClientErrorController@store')->name('client.error.store');
    });

    $router->group(['namespace' => 'System', 'prefix' => 'system'], function ($router) {
        $router->get('config', 'SysConfigController@config')->name('system.config');
        $router->get('adConfig', 'SysConfigController@adConfig')->name('system.adConfig');
    });

    $router->group(['namespace' => 'Wechat', 'prefix' => 'wechat'], function ($router) {
        $router->get('auth', 'WechatController@auth')->name('wechat.auth');
        $router->any('notify', 'WechatController@notify')->name('wechat.notify');
    });

    $router->group(['namespace' => 'Pay', 'prefix' => 'pay'], function ($router) {
        $router->any('alipay-notify', 'PayController@aliPayNotify')->name('alipay.notify');
    });

});

Route::group(['middleware' => 'auth_token'], function (Router $router) {

    $router->group(['namespace' => 'MerUser', 'prefix' => 'user'], function ($router) {
        $router->get('info', 'MerUserController@info')->name('user.info');
        $router->post('out', 'MerUserController@out')->name('user.out');
        $router->put('edit', 'MerUserController@edit')->name('user.edit');
        $router->put('info', 'MerUserController@editInfo')->name('user.edit-info');
        $router->post('game-like', 'MerUserGameLikeController@like')->name('user.game-like');

        $router->post('pay', 'MerUserController@pay')->name('user.pay');

        $router->post('game-collect', 'MerUserGameCollectionController@collect')->name('user.game-collect');
        $router->get('game-collect', 'MerUserGameCollectionController@index')->name('user.game-collect-index');
        $router->get('game-is-collect', 'MerUserGameCollectionController@gameCollect')->name('user.game-collect-is');

        $router->post('game-integral', 'MerUserGameIntegralController@store')->middleware('LaraRsa')->name('user.game-integral');

        $router->post('follow', 'MerUserFollowController@follow')->name('user.follow');
        $router->get('fans', 'MerUserFollowController@index')->name('user.fans');
        $router->get('my-follow', 'MerUserFollowController@myFollow')->name('user.my-follow');

        $router->get('coins', 'MerUserCoinsController@index')->name('user.coins');
        $router->post('obtain', 'MerUserCoinsController@obtain')->name('user.obtain');


    });

    $router->group(['namespace' => 'Game', 'prefix' => 'game'], function ($router) {
        $router->get('/', 'GamePackageController@index')->name('game.index');
        $router->get('subscribe', 'GamePackageController@subscribe')->name('game.subscribe');
    });

    $router->group(['namespace' => 'Topic', 'prefix' => 'topic'], function ($router) {
        $router->get('search', 'TopicController@search')->name('topic.search');

        $router->get('user-topic-list', 'TopicController@userTopicList')->name('topic.user.topic');
        $router->post('/', 'TopicController@follow')->name('topic.follow');

        $router->post('content', 'TopicContentController@publish')->name('topic.content.publish');
        $router->post('cancel-anonymous', 'TopicContentController@cancelAnonymous')->name('topic.content.anonymous');

        $router->get('my-topic-content', 'TopicContentController@myTopicContent')->name('topic.my-topic-content');

        $router->delete('content', 'TopicContentController@delete')->name('topic.content.delete');

        $router->post('comment', 'TopicContentController@comment')->name('topic.comment.publish');
        $router->delete('comment', 'TopicContentController@deleteComment')->name('topic.comment.delete');

        $router->post('comment-like', 'TopicContentController@commentLike')->name('topic.comment.like');
        $router->post('content-like', 'TopicContentController@like')->name('topic.content.like');

        $router->get('notice', 'NoticeController@index')->name('topic.notice.list');
        $router->get('notice-count', 'NoticeController@noticeCount')->name('topic.notice.count');
        $router->get('my-comment', 'TopicContentController@myComment')->name('topic.comment.my-comment');


        $router->post('report', 'TopicContentController@report')->name('topic.content.report');

        $router->post('shield', 'TopicContentController@shield')->name('topic.content.shield');

    });


    $router->group(['namespace' => 'Tool', 'prefix' => 'tool'], function ($router) {

        $router->post('upload', 'CommonController@upload')->name('tool.upload');
    });

    $router->group(['namespace' => 'Pay', 'prefix' => 'pay'], function ($router) {
        $router->get('project', 'PayController@project')->name('pay.project');
        $router->post('/', 'PayController@pay')->name('pay');
        $router->get('pay-order', 'PayController@payOrder')->name('pay.order');
    });


});

Route::any('/captcha', function () {
    return [
        'url' => app('captcha')->create('default', true)
    ];
})->name('captcha');

Route::any('feedback-official', function () {
    return response()->json(request()->all());
});

Route::any('game/list', function () {
    $gamePackage = new \App\Models\Game\GamePackage();
    $gamePackage::$modify = false;

    $list = $gamePackage->orderBy('id', 'desc')->paginate(20);
    $list = $list ? $list->toArray() : [];

//    foreach ($list['data'] as $k => &$v) {
//        $v['icon_img'] = config('filesystems.disks.oss.domain_url') . $v['icon_img'];
//        $v['background_img'] = config('filesystems.disks.oss.domain_url') . $v['background_img'];
//        $gameUrl = env('GAME_URL');
//        $v['url'] = $v['url'] ? $gameUrl . $v['url'] : '';
//        $v['crack_url'] = $v['crack_url'] ? env('CRACK_GAME_URL') . $v['crack_url'] : '';
//    }
    return $list;
})->name('game-list');

Route::any('ad-game/list', function () {

    return [
        [
            'img' => 'https://android-artworks.25pp.com/fs08/2018/02/06/4/106_508240d7599857dec1b1efd5534464ed_con_130x130.png',
            'dec' => '???????????????????????????????????????????????????????????????',
            'title' => '????????????',
            'url' => 'https://www.wandoujia.com/apps/6623588/binding?source=web_seo_baidu_binded',
        ],
        [
            'img' => 'https://android-artworks.25pp.com/fs08/2020/06/01/7/1_64d55e77e2564eb274a55ab64b49b521_con_130x130.png',
            'dec' => '?????????????????????????????????????????????????????????',
            'title' => '???????????????',
            'url' => 'https://www.wandoujia.com/apps/7589903/binding?source=web_seo_baidu_binded',
        ],
        [
            'img' => 'https://android-artworks.25pp.com/fs08/2020/10/14/4/110_38d99afc681a81f7f147e41b96e9935a_con_130x130.png',
            'dec' => '?????????????????????????????????????????????????????????????????????????????????????????????',
            'title' => 'biubiu?????????',
            'url' => 'https://www.wandoujia.com/apps/7854150/binding?source=web_seo_baidu_binded',
        ],
        [
            'img' => 'https://android-artworks.25pp.com/fs08/2019/05/08/10/1_e46c4b93938451b2be8630e7a4dea8e8_con_130x130.png',
            'dec' => '?????????????????????????????????????????????',
            'title' => '????????????',
            'url' => 'https://www.wandoujia.com/apps/7701857/binding?source=web_seo_baidu_binded',
        ],
        [
            'img' => 'https://android-artworks.25pp.com/fs08/2020/06/09/6/109_69cec412575af9cc2d0e60efd8d27924_con_130x130.png',
            'dec' => '?????????????????????????????????????????????????????????????????????????????????',
            'title' => '???????????????',
            'url' => 'https://www.wandoujia.com/apps/6618368/binding?source=web_seo_baidu_binded',
        ]
    ];
})->name('ad-game-list');

Route::any('/test', function () {
    config(['app.timezone' => request()->header('timezone')]);
    \App\Models\Game\AdBurying::query()->create([
        'package_id'=>111
    ]);

    echo"<pre>";print_r(111);exit;
//   echo"<pre>";print_r( (new \Carbon\Carbon(date('Y-m-d H:i:s')))->timezone(config('app.timezone'))->toDateTimeString());exit;
    $request = new \AlibabaCloud\Green\V20180509\ImageSyncScan();
echo"<pre>";print_r($request);exit;
    $res = getHttpContent('post','http://47.242.85.154:81/api/message-send',[
        'to_id' => 'e7jw06BXTDScQQKZPaxDvH:APA91bEUtYW0dkSXGw01ON6I-ghzhJroD0bKU8VSmpRuf6mTjOXqqwUNpAfplT_L35tMNWAdLGUfS2bR3DLU_SHBt2dopvdIubFv9TNQj-vy-wORKbHTLlDIE_l9IWzXxSHBXz6vLBkd',


        'title' => 'FunTouch',
        'body' => 'Someone is spying on you.'
    ]);
    $res = json_decode($res,true);
    echo"<pre>";print_r($res);exit;
    $iClientProfile = \AlibabaCloud\Client\Profile\DefaultProfile::getProfile("cn-shanghai", 'LTAI4GAeD3jcsVmvedfNw922', 'HK3f7xu1gJlo4beVqSE3ygYiEF9qmG'); // TODO
    $client = new \AlibabaCloud\Client\DefaultAcsClient($iClientProfile);

    $request = new \AlibabaCloud\Green\V20180509\ImageSyncScan();

    $request->setMethod("POST");
    $request->setAcceptFormat("JSON");

    $task1 = array('dataId' => uniqid(),
        'url' => 'https://fun-touch.oss-cn-shanghai.aliyuncs.com/game-images/20201229/7GAeoObnPPThWzxeyvmUlQEr6tPDilv3br306zgw.jpeg?x-oss-process=style/yasuo',
        'time' => round(microtime(true) * 1000)
    );
    $request->setContent(json_encode(array("tasks" => array($task1),
        "scenes" => array("porn", "terrorism", 'ad', 'live'))));
    try {
        $response = $client->getAcsResponse($request);
        echo "<pre>";
        print_r($response->data);
        exit;

        if (200 == $response->code) {
            $taskResults = $response->data;

            foreach ($taskResults as $taskResult) {
                if (200 == $taskResult->code) {
                    $taskId = $taskResult->taskId;
                    print_r($taskId);
                    // ???taskId ????????????????????????????????????????????????, ??????ImageAsyncScanResultsRequest
                } else {
                    print_r("task process fail:" + $response->code);
                }
            }
        } else {
            print_r("detect not success. code:" + $response->code);
        }
    } catch (Exception $e) {
        print_r($e);
    }
    exit();

    $googleClient = new \Google_Client();
//    $client->useApplicationDefaultCredentials();
//    $client->setAuthConfig($config_path);
//    $client->setScopes(['https://www.googleapis.com/auth/firebase.messaging']);     # ???????????? FCM ?????????
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
        //?????????
        echo "<pre>";
        print_r($response);
        exit;
    } catch (\Exception $e) {
        throw new \Exception($e->getMessage());
    }
    exit();
    // ??????????????????AccessKey????????????API???????????????????????????????????????????????????????????????RAM????????????API?????????????????????????????????RAM???????????????RAM?????????
    $accessKeyId = "LTAI4GAeD3jcsVmvedfNw922";
    $accessKeySecret = "HK3f7xu1gJlo4beVqSE3ygYiEF9qmG";
// Endpoint????????????????????????Region???????????????????????????
    $endpoint = "http://oss-cn-hangzhou.aliyuncs.com";
    $bucket = "cn-funtouch";
    $object = "germ-squirmish.zip";

// ??????URL??????????????????3600s???
    $timeout = 300;
    try {
        $ossClient = new \OSS\OssClient($accessKeyId, $accessKeySecret, $endpoint, false);

        // ??????GetObject?????????URL???
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
    ini_set('memory_limit', '1000M');

    $file = request()->input('file');
    $data = file_get_contents(storage_path('content/' . $file));
    $data = json_decode($data, true);
//    echo"<pre>";print_r(count($data['data']));exit;
    $count = 0;
    foreach ($data['data'] as $k => $datum) {

        if (isset($datum['pic_urls']) && $datum['pic_urls']) {
            foreach ($datum['pic_urls'] as &$v) {
                $v = importImage('pic/' . $v );
            }
        }
        $user = \App\Models\User\MerUser::query()->firstOrCreate([
            'phone' => $datum['uid']
        ], [
            'nick_name' => $datum['username'],
            'profile_img' => importImage('avatar/' . $datum['avatar_url'] ),
            'sex' => $datum['gender'] == 1 ? 'male' : 'female',
            'birth' => $datum['birthday'],
            'description' => $datum['content'],
        ]);
        if ($user['id'] == 10250){
            continue;
        }

        $content = \App\Models\Topic\TopicContent::query()->create([
            'mer_user_id' => $user->id,
            'content' => $datum['content'],
            'image_resource' => $datum['pic_urls'],
            'is_export' => 1,
            'created_at' => date('Y-m-d H:i:s', $datum['timestamp']),
            'updated_at' => date('Y-m-d H:i:s', $datum['timestamp'])
        ]);

        $topicService = app(TopicService::class);
        if (isset($datum['topicnames']) && $datum['topicnames']) {
            $topicArr = [];
            $datum['topicnames'] = array_slice($datum['topicnames'],0,5);
            foreach ($datum['topicnames'] as $key => $value) {
                $topicArr[] = $topicService->findOrCreate($value,$user->id)->id;
            }
//            $topicArr[] = $topicService->findOrCreate($datum['topicnames'], $user->id)->id;
            $topicArr && $content->topic()->sync($topicArr);
        }

        //????????????
        $gameList = \App\Models\Game\GamePackage::query()
            ->select('id')
            ->where('id', '>=', rand(8, 160))
            ->where('status', '=', 1)
            ->limit(5)
            ->get()
            ->toArray();

        foreach ($gameList as $game) {
            \App\Models\User\MerUserGameHistory::query()->create([
                'uid' => Uuid::uuid1()->toString(),
                'mer_user_id' => $user->id,
                'game_package_id' => $game['id']
            ]);
        }

        //??????????????????
        $gameRankList = \App\Models\Game\GamePackage::query()
            ->select('id','integral_base')
            ->where('id', '>=', rand(8, 160))
            ->where('is_rank', 1)
            ->where('status', '=', 1)
            ->limit(5)
            ->get()
            ->toArray();

        foreach ($gameRankList as $game) {
            \App\Models\User\MerUserGameIntegral::query()->updateOrCreate([
                'mer_user_id' => $user->id,
                'game_package_id' => $game['id']
            ], [
                'integral' => rand(intval($game['integral_base']/2), $game['integral_base']),
                'mer_user_id' => $user->id,
                'game_package_id' => $game['id']
            ]);
        }
        $count++;
    }
    echo '??????' . $count . '?????????';
});

Route::any('/topic-comment', function () {
    set_time_limit(0);
     \App\Models\Topic\TopicContent::query()->where('is_export',1)->chunk(50,function ($item){
         $item = $item->toArray();
         foreach ($item as $key => $value){

             \App\Models\Topic\TopicContent::query()->where('id',$value['id'])->update([
                 'created_at' => date('Y-m-d H:i:s',time()-rand(60*30,60*60*7*24))
             ]);
//             $content = [
//                 'Nah..........hhhh',
//                 'So freaking cure!!!!',
//                 'Crazy',
//                 'that\'s funny',
//                 'i love it ',
//                 'GOOD',
//                 'Hahaha',
//                 'Lol',
//                 'Yep!',
//                 'Wow ???',
//                 'pretty!',
//                 'Amazing',
//                 'Lol that\'s funny',
//                 'Brilliant!',
//                 ' It\'s funny',
//                 'Hell yeah!!!',
//                 'Hahahaha!!!',
//                 'Great',
//                 'Oh hell yes.',
//                 'Nice',
//                 'Crazy!',
//                 'Fantastic',
//                 'That???s so amazing',
//                 'So beautiful.',
//                 'That\'s beautiful!!',
//                 'So pretty!',
//                 'Looks so good!!!',
//                 'Oh wow that looks really really good!',
//                 'Looks amazing',
//                 'Looks awesome!',
//                 'Yum',
//                 'That looks sooooooo yummy!!!',
//                 'Hi',
//                 'Yum!',
//                 ' This looks so good!!',
//                 'so good!',
//                 'Awe!',
//                 'Fantastic',
//                 'So funny',
//                 'Omg!',
//                 'Hey',
//                 'OhOhOh',
//                 'funny!!!',
//                 'Lol funny!!!',
//                 'Very funny!!!',
//                 '????????',
//                 '????',
//                 '?????????????',
//                 '????????????????????',
//                 '????????????',
//                 '????????????',
//                 '????????????',
//                 '????????????',
//                 '????',
//                 '????????????',
//                 '????',
//                 '????????????',
//                 'Omg????????????',
//                 'OMG????????????',
//                 'So funny????????????',
//                 'Fantastic!????????????',
//                 'Awe!????????',
//                 'So good!????????',
//                 'This looks so good!????????',
//                 'Yum!????????????????',
//                 'Love it!????????????',
//                 'Hi????',
//                 'Crazy????????????',
//                 'Nice????????????',
//                 'Oh hell yes.????????????',
//                 'Great????????????',
//                 'Hell yeah!!!????????????',
//                 'YEAL????????????',
//                 'It\'s funny????????????',
//                 'Lol that\'s funny????????????',
//                 'Amazing????????',
//                 'pretty!????',
//                 '????????????',
//                 'Wow.....????????????????',
//                 'Lol that\'s funny????????????',
//                 'Hahaha????????????',
//                 'This looks so good!????????',
//                 '????????????????????????',
//                 '????????????????????',
//                 '????????????',
//                 '??????',
//                 '????????????',
//                 '????????'
//             ];
//             $contentArr = array_random($content,rand(1,count($content)-1));
//             shuffle($contentArr);
//             $userArr = [];
//             if ($value['id']%3 == 0 || $value['id']%7 == 0)
//             {
//                 //????????????
//                 foreach ($contentArr as $comment){
//                     $userId = rand(9990,10187);//rand(9990,10085);
//                     \App\Models\Topic\TopicContentComment::query()->insert([
//                         'content_id' => $value['id'],
//                         'comment' => $comment,
//                         'mer_user_id' => $userId,
//                         'created_at' => date('Y-m-d H:i:s',time()-(rand(1,100000))),
//                         'updated_at' => date('Y-m-d H:i:s',time())
//                     ]);
//                     $userArr[] = $userId;
//                 }
//             }
//
//             for ($i = 0;$i<$value['id']%10;$i++){
//                 $userArr[] = rand(9990,10187);
//             }
//             foreach ($userArr as $user){
//                 \App\Models\Topic\TopicContentLike::query()->firstOrCreate([
//                     'content_id' => $value['id'],
//                     'mer_user_id' => $user
//                 ]);
//                 \App\Models\User\MerUserFollow::query()->firstOrCreate([
//                     'follow_user_id' => $value['mer_user_id'],
//                     'mer_user_id' => $user
//                 ]);
//             }

         }
     });
    return 'success';


});


