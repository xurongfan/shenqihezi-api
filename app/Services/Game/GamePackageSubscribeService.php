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
                $query->selectRaw('id,title,icon_img,background_img,url,is_crack,crack_url,is_landscape,crack_des')
                    ->with(['subscribe' => function($query1){
                        $query1->select('id','game_package_id')->where('mer_user_id',$this->userId())
//                            ->where('end_at','>',date('Y-m-d H:i:s'))
                        ;
                    }]);
            }])
            ->where('mer_user_id',$this->userId())
            ->where('end_at','>',date('Y-m-d H:i:s'))
            ->orderBy('id','desc')->paginate(20)->toArray();
        if ($result){
            foreach ($result['data'] as $key => &$item) {
                $item['game_package']['icon_img'] = ossDomain($item['game_package']['icon_img']);
                $item['game_package']['background_img'] = ossDomain($item['game_package']['background_img']);
                $item['game_package']['url'] = gameUrl($item['game_package']['url']);;
                $item['game_package']['crack_url'] = gameUrl($item['game_package']['crack_url']);
            }
        }

        return $result;
    }
}