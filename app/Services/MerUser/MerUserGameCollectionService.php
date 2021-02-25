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
//            if (!app(MerUserService::class)->isVip()) {
//                if ($this->model->query()->where('mer_user_id',$this->userId())->count() > 5) {
//                    throw new \Exception(transL('mer-user.user_game_collect_limit','收藏数量超额'),501);
//                }
//            }
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
        $page = request()->input('page',1);
        if (!$isVip = app(MerUserService::class)->isVip()) {
//            $page = 1;
//            $limit = 5;
        }

        $result =  $this->model->newQuery()->select('id','game_package_id')
            ->whereHas('gamePackage')
            ->with(['gamePackage' => function($query){
                $query->selectRaw('id,title,icon_img,background_img,url,is_crack,crack_url,is_landscape,crack_des,status')
                    ->with(['subscribe' => function($query1){
                        $query1->select('id','game_package_id')->where('mer_user_id',$this->userId())
                            ->where('end_at','>',date('Y-m-d H:i:s'))
                        ;
                    }]);
            }])
            ->where('mer_user_id',$this->userId())
            ->orderBy('id','desc')
            ->paginate($limit ?? 20,['id', 'game_package_id', 'created_at'],'page',$page)->toArray();
//        if ($result){
//            foreach ($result['data'] as $key => &$item) {
//                $item['game_package']['icon_img'] = ossDomain($item['game_package']['icon_img']);
//                $item['game_package']['background_img'] = ossDomain($item['game_package']['background_img']);
//                $item['game_package']['url'] = gameUrl($item['game_package']['url']);
//                $item['game_package']['crack_url'] = gameUrl($item['game_package']['crack_url'],$item['game_package']['is_crack']);
//            }
//        }
        $result['isVip'] = $isVip;
        return $result;
    }
}