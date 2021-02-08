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
        $data = $this->model->query()
            ->select('amount','created_at','type')
            ->where('mer_user_id',$this->userId())
            ->orderBy('created_at','desc')
            ->paginate(config('app.app_rows'))
            ->toArray();
        if ($data['data']) {
            foreach ($data['data'] as $key => $item){
                $item['symbol'] = in_array($item['type'],[
                    MerUserCoinsLog::TYPE_1,
                    MerUserCoinsLog::TYPE_2,
                    MerUserCoinsLog::TYPE_3,
                    MerUserCoinsLog::TYPE_4
                ]) ? '+' : '-';
            }
        }
        return $data;
    }

    /**
     * @param $type
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function obtain($type)
    {
        $userId = $this->userId();

        if (in_array($type,[MerUserCoinsLog::TYPE_1,MerUserCoinsLog::TYPE_2])){

            $info = MerUserInfo::query()->firstOrCreate([
                'mer_user_id' => $userId
            ]);
            if ($type == MerUserCoinsLog::TYPE_1){
                if ($info->first_wechat_bind == 1){
                    throw new \Exception(transL('common.coins_get_over'));
                }
                $info->first_wechat_bind = 1;
            }
            if ($type == MerUserCoinsLog::TYPE_2){
                if ($info->first_play_game == 1){
                    throw new \Exception(transL('common.coins_get_over'));
                }
                $info->first_play_game = 1;
            }

            $info->save();
        }
        return $this->coins($userId,$type);
    }
    /**
     * @param $merUserId
     * @param $amount
     * @param $type
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function coins($merUserId,$type,$amount = 0)
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
                $amount = MerUserCoinsLog::TYPE_COINS[$type];
                $user->coins += $amount;
            }else{
                if ($amount > $user->coins){
                    throw new \Exception(transL('common.lack_of_coins'));
                }
                $user->coins -= $amount;
            }
            $this->save([
                'mer_user_id' => $merUserId,
                'type' => $type,
                'before_operate_amount' => $user->getOriginal('coins'),
                'amount' => $amount,
                'after_operate_amount' => $user->coins,
            ]) && $user->save();

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