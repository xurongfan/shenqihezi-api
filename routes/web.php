<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    abort(404);
    return view('welcome');
});


Route::get('config',function (){
    echo"<pre>";print_r(222);exit;
});

Route::get('config/{key}', 'System\SysConfigController@viewConfig');
