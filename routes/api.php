<?php
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

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

});

Route::group(['middleware' => 'auth_token'],function (Router $router){

    $router->group(['namespace' => 'MerUser','prefix' => 'user'],function ($router){
        $router->get('info', 'MerUserController@info')->name('user.info');
        $router->post('out', 'MerUserController@out')->name('user.out');
        $router->put('edit', 'MerUserController@edit')->name('user.edit');

        $router->post('game-like', 'MerUserGameLikeController@like')->name('user.game-like');

        $router->post('game-collect', 'MerUserGameCollectionController@collect')->name('user.game-collect');
        $router->get('game-collect', 'MerUserGameCollectionController@index')->name('user.game-collect-index');

        $router->get('game-history', 'MerUserGameHistoryController@index')->name('user.game-history-index');
        $router->post('game-history', 'MerUserGameHistoryController@store')->name('user.game-history-store');
        $router->put('game-history/{uid}', 'MerUserGameHistoryController@report')->name('user.game-history-report');

    });


    $router->group(['namespace' => 'Game','prefix' => 'game'],function ($router){
        $router->get('/', 'GamePackageController@index')->name('game.index');
    });

    $router->group(['namespace' => 'System','prefix' => 'system'],function ($router){
        $router->get('config', 'SysConfigController@config')->name('system.config');
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
        $v['url'] = 'http://h5-cdn.sqhezi.cn/'.$v['url'];
        $v['crack_url'] = $v['crack_url']  ? 'http://h5-cdn.sqhezi.cn/'.$v['crack_url'] : '';
    }
    return $list;
})->name('game-list');

