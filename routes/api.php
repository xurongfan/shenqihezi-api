<?php

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

Route::options('/{all}', function(Request $request) {
    return response('options here!');
})->where(['all' => '([a-zA-Z0-9-]|/)+']);

Route::post('/auth/login', 'Auth\AdminUserController@login')->name('auth.login');
Route::any('/captcha', function (){
    return [
        'url' => app('captcha')->create('default', true)
    ];
})->name('captcha');

Route::get('/gameList', function (){
  $list =  \App\Model\Game\PackageGame::query()->paginate(10)->toArray();

  foreach ($list['data'] as $k => &$datum) {
    if (!$datum['is_h']) {
      $datum['icon'] = env('ALIOSS_URL').$datum['package_name'].'/'.$datum['icon'];
      $datum['banner'] = env('ALIOSS_URL').$datum['package_name'].'/'.$datum['banner'];
      $datum['imgs'] = $datum['imgs'] ? json_decode($datum['imgs'],true) : [];
      $datum['game_url'] = env('ALIOSS_URL').$datum['package_name'].'/'.$datum['package_name'].'.apk';
      foreach ($datum['imgs'] as &$img) {
        $img =  env('ALIOSS_URL').$datum['package_name'].'/'.$img;
      }
    }else{
      $datum['icon'] = env('H5_URL').$datum['package_name'].'/'.$datum['icon'];
      $datum['banner'] = env('H5_URL').$datum['package_name'].'/'.$datum['banner'];
      $datum['game_url'] = env('H5_URL').$datum['package_name'].'/game/';
    }

  }

  return $list;
})->name('game-list');

Route::any('/test', function (){
  function my_scandir($dir)
  {
    //定义一个数组
    $files = array();
    //检测是否存在文件
    if (is_dir($dir)) {
      //打开目录
      if ($handle = opendir($dir)) {
        //返回当前文件的条目
        while (($file = readdir($handle)) !== false) {
          //去除特殊目录
          if ($file != "." && $file != "..") {
            //判断子目录是否还存在子目录
            if (is_dir($dir . "/" . $file)) {
              //递归调用本函数，再次获取目录
              $files[$file] = my_scandir($dir . "/" . $file);
            } else {
              //获取目录数组
              $files[] = $file;
            }
          }
        }
        //关闭文件夹
        closedir($handle);
        //返回文件夹数组
        return $files;
      }
    }
  }

  $data = my_scandir("/var/www/public/package");
//  echo"<pre>";print_r($data);exit;
  foreach ($data as $k => &$v){

    if (is_array($v) && $k == 'com.ylungamestudio.breakinggatespassmb'){
      $img_arr = [];
      foreach ($v as $key => $item){

        if ($item == 'gameinfo.json'){
          $info = file_get_contents('/var/www/public/package/'.$k.'/'.$item);
          echo"<pre>";print_r($info);exit;

          $info = json_decode($info,true);

        }
        if (strpos($item,'img') !== false){
        $img_arr[] = $item;
        }
      }

      DB::table('package_game')->insert([
        'title' => $info['title'] ?? '',
        'description' => $info['description'] ?? '',
        'package_name' => $info['package_name']??'',
        'icon' => 'icon.png',
        'banner' => 'banner.png',
        'imgs' => $img_arr ? json_encode($img_arr) : '',
      ]);

    }

  }
  exit();
});

Route::group(['middleware' => 'auth_token'],function (){

    Route::group(['namespace' => 'Auth','prefix' => 'auth'],function (){
        Route::get('/user', 'AdminUserController@user')->name('auth.user');
        Route::post('/logout', 'AdminUserController@logout')->name('auth.logout');
    });



    Route::group(['namespace' => 'Tool','prefix' => 'tool'],function (){
        Route::post('/upload', 'CommonController@baiShanCloud')->name('tool.baiShanCloud');
    });

});


