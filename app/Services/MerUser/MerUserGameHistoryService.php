<?php

namespace App\Services\MerUser;

use App\Base\Services\BaseService;
use App\Models\User\MerUserInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class MerUserGameHistoryService extends BaseService
{
    /**
     * @param $userId
     * @return mixed
     */
    public function index($userId = 0)
    {
        $page = request()->input('page',1);
        if (!$isVip = app(MerUserService::class)->isVip() && !$userId) {
//            $page = 1;
//            $limit = 5;
        }
        //查看他人历史
        if ($userId) {
            $limit = 5;
        }

        $userId = $userId ? $userId : $this->userId();

        $result = $this->model->newQuery()
        ->whereHas('gamePackage')
        ->with(['gamePackage' => function ($query) {
            $query->selectRaw('id,title,icon_img,background_img,url,is_crack,crack_url,is_landscape,crack_des,status')
            ->with(['subscribe' => function($query1){
                $query1->select('id','game_package_id')->where('mer_user_id',$this->userId())
                    ->where('end_at','>',date('Y-m-d H:i:s'))
                ;
            }]);
        }])
        ->where('mer_user_id', $userId)
        ->orderBy('id', 'desc')
        ->groupBy('game_package_id')
        ->paginate($limit ?? 50,['id', 'game_package_id', 'created_at'],'page',1)->toArray();
//        $result['isVip'] = $isVip;
        return $result;
    }

    /**
     * @param $gamePackageId
     * @param null $uid
     * @return \App\Base\Services\BaseModel
     * @throws \Exception
     */
    public function store($gamePackageId)
    {
        return $this->save([
            'mer_user_id' => $this->userId(),
            'game_package_id' => $gamePackageId,
            'uid' => Uuid::uuid1()->toString(),
        ]);
    }

    /**
     * @param $gamePackageId
     * @param $uid
     * @param int $duration
     * @return \App\Base\Services\BaseModel
     * @throws \Exception
     */
    public function report($gamePackageId,$uid,$duration=0)
    {
        if ($report = $this->findOneBy(['uid' => $uid])){
//            $report['created_at'] = Carbon::parse($report['created_at']);
            $time = $duration + $report['duration'];
            $this->updateBy(
                [
                    'mer_user_id' => $this->userId(),
                    'game_package_id' => $gamePackageId,
                    'uid' => $uid
                ],
                [
                    'duration' => $time//$duration$duration ? $duration : (new Carbon())->diffInSeconds($report['created_at'])
                ]
            );
            //更新用户总游戏时长
            MerUserInfo::query()->where('mer_user_id',$report['mer_user_id'])
                ->increment('total_game_time',$duration);

            return $report;
        }
        throw new \Exception(transL('common.system_error'));
    }

    /**
     * @return mixed
     */
    public function hotGame()
    {
        return Cache::remember('hot-game-list', 60*2, function () {
            $result = $this->model->newQuery()
                ->select('game_package_id',DB::raw('sum(`duration`) as score') )
//            ->where('duration','>',30)
                ->with(['gamePackage'=>function($query){
                    $query->select('id','title','des','icon_img','background_img','url','is_crack','crack_url','is_landscape','is_rank','crack_des','video_url','status');
                }])
                ->whereHasIn('gamePackage',function($query){
                    $query->where('is_rank',1)->where('status',1);
                })
                ->groupBy('game_package_id')
                ->orderBy(DB::raw('score'),'desc')
                ->get()->toArray();

            return $result;
        });
    }

    /**
     * @return mixed
     */
    public function hotTopGame()
    {
        $hotGame = $this->hotGame();
        return $hotGame[array_rand($hotGame,1)];
    }

}