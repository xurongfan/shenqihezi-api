<?php
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
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
        $router->post('login', 'MerUserController@login')->name('user.login');
    });

    $router->group(['namespace' => 'Game','prefix' => 'game'],function ($router){
        $router->get('tag', 'GameTagController@all')->name('game.tag');
    });

    $router->group(['namespace' => 'System','prefix' => 'system'],function ($router){
        $router->get('config', 'SysConfigController@config')->name('system.config');
    });

});

Route::group(['middleware' => 'auth_token'],function (Router $router){

    $router->group(['namespace' => 'MerUser','prefix' => 'user'],function ($router){
        $router->get('info', 'MerUserController@info')->name('user.info');
        $router->post('out', 'MerUserController@out')->name('user.out');
        $router->put('edit', 'MerUserController@edit')->name('user.edit');
        $router->get('/info/{id}', 'MerUserController@user')->name('user.show');
        $router->post('game-like', 'MerUserGameLikeController@like')->name('user.game-like');

        $router->post('game-collect', 'MerUserGameCollectionController@collect')->name('user.game-collect');
        $router->get('game-collect', 'MerUserGameCollectionController@index')->name('user.game-collect-index');

        $router->get('game-history', 'MerUserGameHistoryController@index')->name('user.game-history-index');
        $router->post('game-history', 'MerUserGameHistoryController@store')->name('user.game-history-store');
        $router->put('game-history/{uid}', 'MerUserGameHistoryController@report')->name('user.game-history-report');
        $router->get('game-hot', 'MerUserGameHistoryController@hotGame')->name('user.game-hot');

        $router->post('game-integral', 'MerUserGameIntegralController@store')->name('user.game-integral');
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


    $router->group(['namespace' => 'Tool','prefix' => 'tool'],function ($router){

        $router->post('upload', 'CommonController@upload')->name('tool.upload');
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
        $gameUrl = $v['url_type'] == 1 ? env('GAME_URL') : 'http://fun-touch.oss-cn-shanghai.aliyuncs.com/';
        $v['url'] = $v['url'] ? $gameUrl.$v['url'] : '';
        $v['crack_url'] = $v['crack_url']  ? $gameUrl.$v['crack_url'] : '';
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


    $googleClient = new \Google_Client();
    $googleClient->setScopes([\Google_Service_AndroidPublisher::ANDROIDPUBLISHER]);
    $googleClient->setApplicationName('Your_Purchase_Validator_Name');
    $googleClient->setAuthConfig('{
  "type": "service_account",
  "project_id": "able-device-293401",
  "private_key_id": "fa4f600d12bb216bd6077d34537bb23ed350b6a6",
  "private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCJYRGemSITqgHL\n22H3g1ntQe7XokRlBUM7ujYmX4LNxZse9bp6AvoHlBf1H5BcaRR/R0IganSgr807\nu8AGfZ1A0/a+ZjZ+7oZCAfP37rfQtk8U2DWTcSn1S8fIfGdgUePdFsczdzI+ik7F\nBElrPbU02wH8HMgJ9qwECWB++GmLYKBM5DgcldHi+AZ4YMknDKL1ZF4EFIWldM2C\naRVBzjuqdtH2c3eeG+vMgIt1ApJoeHeYalOXDL9xZSZMNpENBbh5M7vNDoiwTUQn\n6UG/cUxrNZZMuJdlV6BKUm2cgOdPbtMJvVwkPgDLW/P78wk9IRKIREendBvzTGnF\n134lmThjAgMBAAECggEACQPz6463bpyYB0BLkrot0xpMvx2Y08ZUzgfKLkTTU9yq\nxuKFDSUX3Rp0uLM3E+bDdReAjpmzM1Ow1MGH+FpoUPeiuDEDVNt4t76NZlEQrWOM\nF11EUCY7hNZRyJA9b9PqmmONyWICS+9jUZ5BO9vuAdzOKbTb0yeQd1Ku4KDoZZXm\nS66EZq7TFhO2sQXr+ag1xblZxGlmHtN9N2IBrsZ5Kqmx+WfgigQEc1vuWMg8qVGl\nN8nmyKUflRtktLABwCFMuN++hD2JvMAeatVTwX1r/vigBhM+jLahyF1wPXxQPG0t\nxS0TdSFcJJ7ZE30CdgOEyWrHC0+MqdbFh1fwsREEIQKBgQC+0+CZwd2l28INw/Cu\n3eJYL04L6RYXNfykVdrUoEs+GCdnwHKSuO+CzZyhUTH4jqWZTNSjj66gRXc38wXt\n06EfLhJjMSj790RiZE3ao8EtitPa0ABEQ28C65+iTdk1EMpWbkdH4AOnmMPKdqK1\n4lwR+dTNd7JUlrB0dKC1FASp4QKBgQC4TCynrX44S7vuwqCiV3CVXEbxIFTHK906\nnLG+i7JvH8F+PpVtkTPXt2joQxv1/VxYYa48lyRUtYTiK8pwj8nB8o+/pRHn5Tgx\nZjXUroL2i74GDFBmQwXICdWwPRKY0F7kaipMSRmQhpef7CUqDcwRS9Mzd7H8nK4X\nTFhI+VoSwwKBgBf2t4/XfqQjcr0TzBfJmXEFj8GDJdkIWI+ykGZ/MrO7iMdIrZqr\nSricZX0Em0fhcf5MXa+kjYNm9c+63xW8q9Ekkf6O39y0cowAmJ5KTioP8wbZdA8e\ngMRXHpbdO9ekIiS0eJMYGJ1lW8EDIO/CW1mOjCC5VVW2DraxJrVWrDdBAoGAF5ra\nFVfpVLiOh5QyEtj0OI0rIPMtKJ17pmgvc+JcplMA63SEmxX/998r9qOxzx32V/Oa\n53PMWXUuYfGN6kDgbJDuzHMOCg+X1OvsdSMs7vsTCZ9GJPLsqKRp1DreSOhXXxYh\n+MdcGODERt1uHSbLPmPh1zO7fklrGtzSafZWDRUCgYBMAJetK9pl+r4FrWQt5QuO\nh8rBKKmbUDEGCUzN3+sHKx6LJMY9PEL2xvYNiHIz6x8dJoAniedPCOAeBRZZCJOL\nLEYwxKoSTxKCQdcw2RYaaPAZZjz0hQKjencA/Z1VTz7HZlcqh3ORBWpIQdsQ4zzy\n/QdjsJhmmMchCC0Xd1Q9HQ==\n-----END PRIVATE KEY-----\n",
  "client_email": "id-631@able-device-293401.iam.gserviceaccount.com",
  "client_id": "103002766407790987728",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/id-631%40able-device-293401.iam.gserviceaccount.com"
}
');

    $googleAndroidPublisher = new \Google_Service_AndroidPublisher($googleClient);
    $validator = new \ReceiptValidator\GooglePlay\Validator($googleAndroidPublisher);

    try {
        $response = $validator->setPackageName('com.magic.taper')
            ->setProductId('ceshi1')
            ->setPurchaseToken('hbofoepfkmnkabeokneoanlb.AO-J1Ow_x43AB_IS-ORmazsVTrUbNVi3oq0fdBKlKX0PmvoIhNHaKoCB94BoZ52557WVYji5YDg3m-aCArRdr9G3H76b2ihYAQ')
            ->validateSubscription();
        echo"<pre>";print_r($response);exit;
    } catch (\Exception $e){
        var_dump($e->getMessage());
        // example message: Error calling GET ....: (404) Product not found for this application.
    }
// success
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
    $config = [
        // 必要配置
        'app_id'             => 'wx9570383f3e10adb1',
        'mch_id'             => '1604486511',
        'key'                => '4a9f2c433adcc2698ba7704faedeaf82',   // API 密钥

        'notify_url'         => 'http://api.sqhezi.cn/api/test',     // 你也可以在下单时单独设置来想覆盖它
    ];
    $app = \EasyWeChat\Factory::payment($config);

    $result = $app->order->unify([
        'body' => 'test11',
        'out_trade_no' => '20150806125346',
        'total_fee' => 88,
        'trade_type' => 'APP', // 请对应换成你的支付方式对应的值类型
    ]);

    echo"<pre>";print_r($result);exit;
});