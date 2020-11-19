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


//Route::get('happy-glass-3a', function () {
//    return file_get_contents('/var/www/public/shenqihezi/happy-glass-3a/index.html');
//});