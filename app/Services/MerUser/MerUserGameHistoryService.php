<?php

namespace App\Services\MerUser;

use App\Base\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class MerUserGameHistoryService extends BaseService
{
    /**
     * @return mixed
     */
    public function index()
    {
        $result = $this->model->newQuery()->select('id', 'game_package_id', 'created_at')
            ->whereHas('gamePackage')
            ->with(['gamePackage' => function ($query) {
                $query->selectRaw('id,title,icon_img,background_img,url,is_crack,crack_url,is_landscape,crack_des');
            }])
            ->where('mer_user_id', $this->userId())
            ->orderBy('id', 'desc')->paginate(20)->toArray();
        if ($result) {
            foreach ($result['data'] as $key => &$item) {
                $item['game_package']['icon_img'] = ossDomain($item['game_package']['icon_img']);
                $item['game_package']['background_img'] = ossDomain($item['game_package']['background_img']);
                $item['game_package']['url'] = config('app.game_url') . $item['game_package']['url'];
                $item['game_package']['crack_url'] = $item['game_package']['crack_url'] ? config('app.game_url') . $item['game_package']['crack_url'] : '';
            }
        }

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
     * @param $duration
     */
    public function report($gamePackageId,$uid,$duration)
    {
        if ($report = $this->findOneBy(['uid' => $uid])){
            $this->updateBy(
                [
                    'mer_user_id' => $this->userId(),
                    'game_package_id' => $gamePackageId,
                    'uid' => $uid
                ],
                [
                    'duration' => $duration
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
            ->whereHasIn('gamePackage')
            ->groupBy('game_package_id')
            ->orderBy(DB::raw('score'),'desc')
            ->get()->toArray();

        if ($result) {
            foreach ($result as $key => &$item) {
                $item['game_package']['icon_img'] = ossDomain($item['game_package']['icon_img']);
                $item['game_package']['background_img'] = ossDomain($item['game_package']['background_img']);
                $item['game_package']['url'] = config('app.game_url').$item['game_package']['url'];
                $item['game_package']['crack_url'] = $item['game_package']['crack_url'] ? config('app.game_url').$item['game_package']['crack_url'] : '';
            }
        }

        return $result;
    }
}