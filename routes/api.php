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


Route::group(['namespace' => 'MerUser','prefix' => 'user'],function (Router $router){

    $router->post('reg', 'MerUserController@reg')->name('user.reg');
    $router->post('sendSms', 'MerUserController@sendSms')->name('user.sms');
    $router->post('login', 'MerUserController@login')->name('user.login');

});

Route::group(['middleware' => 'auth_token'],function (Router $router){

    $router->group(['namespace' => 'MerUser','prefix' => 'user'],function ($router){
        $router->get('info', 'MerUserController@info')->name('user.info');
        $router->post('out', 'MerUserController@out')->name('user.out');
        $router->put('edit', 'MerUserController@edit')->name('user.edit');

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

