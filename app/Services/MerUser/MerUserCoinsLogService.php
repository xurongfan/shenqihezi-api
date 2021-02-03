<?php

namespace App\Services\MerUser;

use App\Base\Services\BaseService;
use App\Models\User\MerUserCoinsLog;
use App\Models\User\MerUserInfo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MerUserCoinsLogService extends BaseService
{
    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        return $this->model->query()
            ->select('amount','created_at','type')
            ->where('mer_user_id',$this->userId())
            ->paginate(config('app.app_rows'))->each(function ($item, $key) {

                $item['symbol'] = in_array($item['type'],[
                    MerUserCoinsLog::TYPE_1,
                    MerUserCoinsLog::TYPE_2,
                    MerUserCoinsLog::TYPE_3,
                    MerUserCoinsLog::TYPE_4
                ]) ? '+' : '-';

            });
    }
    /**
     * @param $merUserId
     * @param $amount
     * @param $type
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function coins($merUserId,$amount,$type)
    {
        $key = 'coins_log:' . $merUserId;
        if (!Cache::add($key, 1, 10)) {
            throw new \Exception(transL('common.system_busy'));
        }
        DB::beginTransaction();
        try {
            $user = MerUserInfo::query()->firstOrCreate([
                'mer_user_id' => $merUserId
            ]);
            if (in_array($type,[
                MerUserCoinsLog::TYPE_1,
                MerUserCoinsLog::TYPE_2,
                MerUserCoinsLog::TYPE_3,
                MerUserCoinsLog::TYPE_4
            ])) {
                $user->coins += $amount;
            }else{
                if ($amount > $user->coins){
                    throw new \Exception(transL('common.lack_of_coins'));
                }
                $user->coins -= $amount;
            }
            $user->save();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getMessage());
        } finally {
            Cache::forget($key);
        }

        return $user;
    }

}