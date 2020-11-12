<?php

namespace App\Services\MerUser;

use App\Base\Services\BaseService;
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
        throw new \Exception('12212');
    }
}