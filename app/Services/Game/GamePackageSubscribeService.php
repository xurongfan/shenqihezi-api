<?php

namespace App\Services\Game;

use App\Base\Services\BaseService;

class GamePackageSubscribeService extends BaseService
{
    /**
     * 订阅列表
     * @return mixed
     */
    public function index()
    {
        $result =  $this->model->newQuery()->select('id','game_package_id')
//            ->whereHas('gamePackage')
            ->with(['gamePackage' => function($query){
                $query->selectRaw('id,title,icon_img,background_img,url,is_crack,crack_url,is_landscape,crack_des');
            }])
            ->where('mer_user_id',$this->userId())
            ->orderBy('id','desc')->paginate(20)->toArray();
        if ($result){
            foreach ($result['data'] as $key => &$item) {
                $item['game_package']['icon_img'] = ossDomain($item['game_package']['icon_img']);
                $item['game_package']['background_img'] = ossDomain($item['game_package']['background_img']);
                $item['game_package']['url'] = config('app.game_url').$item['game_package']['url'];
                $item['game_package']['crack_url'] = $item['game_package']['crack_url'] ? config('app.game_url').$item['game_package']['crack_url'] : '';
            }
        }

        return $result;
    }
}