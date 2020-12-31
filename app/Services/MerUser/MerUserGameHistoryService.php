<?php

namespace App\Services\MerUser;

use App\Base\Services\BaseService;
use Carbon\Carbon;
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
            $page = 1;
            $limit = 5;
        }
        //查看他人历史
        if ($userId) {
            $limit = 5;
        }

        $userId = $userId ? $userId : $this->userId();

        $result = $this->model->newQuery()
        ->whereHas('gamePackage')
        ->with(['gamePackage' => function ($query) {
            $query->selectRaw('id,title,icon_img,background_img,url,is_crack,crack_url,is_landscape,crack_des,status');
        }])
        ->where('mer_user_id', $userId)
        ->orderBy('id', 'desc')
        ->groupBy('game_package_id')
        ->paginate($limit ?? 20,['id', 'game_package_id', 'created_at'],'page',1)->toArray();
        if ($result) {
            foreach ($result['data'] as $key => &$item) {
                $item['game_package']['icon_img'] = ossDomain($item['game_package']['icon_img']);
                $item['game_package']['background_img'] = ossDomain($item['game_package']['background_img']);
                $item['game_package']['url'] = gameUrl($item['game_package']['url']);
                $item['game_package']['crack_url'] = gameUrl( $item['game_package']['crack_url']);
            }
        }

        $result['isVip'] = $isVip;
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
     */
    public function report($gamePackageId,$uid)
    {
        if ($report = $this->findOneBy(['uid' => $uid])){
            $report['created_at'] = Carbon::parse($report['created_at']);
            $this->updateBy(
                [
                    'mer_user_id' => $this->userId(),
                    'game_package_id' => $gamePackageId,
                    'uid' => $uid
                ],
                [
                    'duration' => (new Carbon())->diffInSeconds($report['created_at'])
                ]
            );
            return $report;
        }
        throw new \Exception(transL('common.system_error'));
    }

    /**
     * @return array
     */
    public function hotGame()
    {
        $result = $this->model->newQuery()
            ->select('game_package_id',DB::raw('sum(`duration`) as score') )
//            ->where('duration','>',30)
            ->with(['gamePackage'=>function($query){
                $query->select('id','title','icon_img','background_img','url','is_crack','crack_url','is_landscape','is_rank','crack_des');
            }])
            ->whereHasIn('gamePackage',function($query){
                $query->where('is_rank',1);
            })
            ->groupBy('game_package_id')
            ->orderBy(DB::raw('score'),'desc')
            ->get()->toArray();

        if ($result) {
            foreach ($result as $key => &$item) {
                $item['game_package']['icon_img'] = ossDomain($item['game_package']['icon_img']);
                $item['game_package']['background_img'] = ossDomain($item['game_package']['background_img']);
                $item['game_package']['url'] = gameUrl($item['game_package']['url']);
                $item['game_package']['crack_url'] = gameUrl($item['game_package']['crack_url']);
            }
        }

        return $result;
    }
}