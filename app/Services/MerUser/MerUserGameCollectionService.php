<?php

namespace App\Services\MerUser;

use App\Base\Services\BaseService;

class MerUserGameCollectionService extends BaseService
{
    /**
     * @param $gamePackageId
     * @return mixed
     */
    public function collect($gamePackageId)
    {
        if ($res = $this->findOneBy( [
            'mer_user_id' => $this->userId(),
            'game_package_id' => $gamePackageId
        ])) {
            $res->delete();
        }else{
            //收藏数量限制
            if (!app(MerUserService::class)->isVip()) {
                if ($this->model->query()->where('mer_user_id',$this->userId())->count() > 5) {
                    throw new \Exception(transL('mer-user.user_game_collect_limit','收藏数量超额'),501);
                }
            }
            return $this->save( [
                'mer_user_id' => $this->userId(),
                'game_package_id' => $gamePackageId
            ]);
        }
        return ;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $result =  $this->model->newQuery()->select('id','game_package_id')
            ->whereHas('gamePackage')
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