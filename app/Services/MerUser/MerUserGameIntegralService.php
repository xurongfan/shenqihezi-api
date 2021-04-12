<?php

namespace App\Services\MerUser;

use App\Base\Services\BaseService;
use Illuminate\Support\Facades\DB;

class MerUserGameIntegralService extends BaseService
{
    /**
     * @param $gamePackageId
     * @param int $integral
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function integral($gamePackageId , $integral = 0)
    {
        $integralInfo = $this->model->newQuery()->firstOrCreate(
            [
                'mer_user_id' => $this->userId(),
                'game_package_id' => $gamePackageId
            ],
            [
                'integral' => $integral
            ]
        );
        if ($integral > $integralInfo['integral']) {
            $integralInfo->update([
                'integral' => intval($integral)
            ]);
        }
        return $this->gameUserRank($gamePackageId);
    }

    /**
     * @param $gamePackageId
     * @return array
     */
    public function integralRank($gamePackageId)
    {
        $rankList = $this->model->newQuery()
            ->select('mer_user_id','game_package_id','integral')
//            ->with(['gamePackage'=>function($query){
//              $query->select('id','title');
//            }])
            ->with(['user'=>function($query){
                $query->select('id','nick_name','profile_img');
            }])
            ->where('game_package_id',$gamePackageId)
            ->orderBy('integral','desc')->limit(50)->get();

//        $myIntegral = $this->userPackageIntegral($gamePackageId);
//        if ($myIntegral) {
//            $rank = $this->model->newQuery()
//                ->where('game_package_id',$gamePackageId)
//                ->where('integral','>',$myIntegral)
//                ->count();
//        }
        if ($this->userId()){
            $userRank = $this->gameUserRank($gamePackageId);
        }
        return [
            'rankList' => $rankList,
            'myRank' => [
                'integral' => $userRank['myIntegral'] ?? 0,
                'rank' => $userRank['rank'] ?? 0 ,
            ]
        ];
    }

    /**
     * @param $gamePackageId
     * @return array
     */
    public function gameUserRank($gamePackageId)
    {
        $myIntegral = $this->userPackageIntegral($gamePackageId);
        $rank = null;
        if ($myIntegral) {
            $rank = $this->model->newQuery()
                ->where('game_package_id',$gamePackageId)
                ->where('integral','>',$myIntegral)
                ->count();
        }
        $rank = !is_null($rank) ? ($rank+1) : 0;
        return compact('myIntegral','rank');
    }

    /**
     * @param $gamePackageId
     * @return int
     */
    public function userPackageIntegral($gamePackageId)
    {
        $rankLog = $this->findOneBy([
            'mer_user_id' => $this->userId(),
            'game_package_id' => $gamePackageId
        ]);
        return $rankLog['integral'] ?? 0;
    }
}