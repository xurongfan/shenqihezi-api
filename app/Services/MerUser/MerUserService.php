<?php

namespace App\Services\MerUser;

use App\Base\Services\BaseService;
use App\Models\User\MerUserGameHistory;
use App\Models\User\MerUserGameLog;
use App\Models\User\MerUserInfo;
use App\Models\User\MerUserLoginLog;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class MerUserService extends BaseService
{
    /**
     * 发动短信
     * @param string $type
     * @param $phone
     * @param string $areaCode
     * @throws \AlibabaCloud\Client\Exception\ClientException
     */
    public function sendSmsCode($phone, $areaCode = '', $type = 'login')
    {

        $keys = request()->only('facebook_auth_code', 'google_auth_code', 'wechat_auth_code');
        if ($keys) {
            if (self::finOneUser($keys)) {
                throw new \Exception(transL('mer-user.user_exist_from_third', '用户已存在'));
            }

            if ($user = $this->getUserByPhone($phone, $areaCode)) {
                foreach ($keys as $k => $key) {
                    if (isset($user[$k]) && $user[$k]) {
                        throw new \Exception(transL('mer-user.user_exist_from_third', '用户已存在'));
                    }
                }
            }
        }

        $varifyCode = mt_rand(1000, 9999);
        if (sendSms($phone, $areaCode, $varifyCode)) {
            Redis::SETEX(self::smsKey($areaCode . $phone, $type), 300, $varifyCode);
            return;
        }
    }


    /**
     * @param string $type
     * @param $phoneNumber
     * @return string
     */
    public function smsKey($phoneNumber, $type = 'login')
    {
        return $type . '_' . $phoneNumber;
    }

    /**
     * 注册
     * @param $request
     * @return \App\Base\Models\BaseModel|\App\Base\Services\BaseModel
     */
    public function reg($request)
    {
        if ($this->getUserByPhone($request['phone'], $request['area_code'])) {
            throw new \Exception(transL('mer-user.user_exist', '用户已存在'));
        }
        //验证码校验
        if (($request['verify_code'] ?? '') != Redis::GET(self::smsKey($request['area_code'] . $request['phone'], 'login'))) {
            throw new \Exception(transL('sms.sms_code_error'));
        }
        $keys = Arr::only($request, ['facebook_auth_code', 'google_auth_code', 'wechat_auth_code']);
        if ($keys && self::finOneUser(array_filter($keys))) {
            throw new \Exception(transL('mer-user.user_exist_from_third', '用户已存在'));
        }
        $data = $this->model->filter($request);

//        $data['last_login_ip'] = request()->getClientIp();
//        $data['last_login_date'] = Carbon::now()->toDateTimeString();

        $this->model->fill($data)->save();
        $this->model->tags()->sync($request['tags']);
        $this->model->userInfo()->create([
            'referrer' => $request['referrer'] ?? '',
        ]);
        //生成token
        $this->model->token = 'Bearer ' . self::loginToken($this->model);

        $this->cacheToken($this->model);

        return $this->model->toArray();
    }

    /**
     * 登录
     * @param $request
     * @return mixed
     * @throws \Exception
     */
    public function login($request)
    {
        if (isset($request['phone']) && $request['phone']) {
            //验证码校验
            if (($request['verify_code'] ?? '') != Redis::GET(self::smsKey($request['area_code'] . $request['phone'], 'login'))) {
                throw new \Exception(transL('sms.sms_code_error'));
            }
            $user = $this->getUserByPhone($request['phone'], $request['area_code']);
            if (!isset($user)) {
                throw new \Exception(transL('mer-user.user_not_exist'), 100);
            }
            //检查第三方key是否已被注册
            $keys = array_filter(Arr::only($request, ['facebook_auth_code', 'google_auth_code', 'wechat_auth_code']));
            foreach ($keys as $k => $key) {
                if (isset($user[$k]) && $user[$k]) {
                    throw new \Exception(transL('mer-user.user_exist_from_third', '用户已存在'));
                }
            }
            if ($keys && self::finOneUser($keys)) {
                throw new \Exception(transL('mer-user.user_exist_from_third', '用户已存在'));
            }

            $user->update($keys);
        } else if (isset($request['facebook_auth_code']) && $request['facebook_auth_code']) {
            $user = $this->finOneUser(['facebook_auth_code' => $request['facebook_auth_code']]);
            if (!isset($user)) {
                throw new \Exception(transL('mer-user.third_login_user_not_exist'), 100);
            }
        } else if (isset($request['google_auth_code']) && $request['google_auth_code']) {
            $user = $this->finOneUser(['google_auth_code' => $request['google_auth_code']]);
            if (!isset($user)) {
                throw new \Exception(transL('mer-user.third_login_user_not_exist'), 100);
            }
        } else if (isset($request['wechat_auth_code']) && $request['wechat_auth_code']) {
            $user = $this->finOneUser(['wechat_auth_code' => $request['wechat_auth_code']]);
            if (!isset($user)) {
                throw new \Exception(transL('mer-user.third_login_user_not_exist'), 100);
            }
        }
        $user->token = 'Bearer ' . self::loginToken($user);
        $this->cacheToken($user);


        return $user;
    }

    /**
     * @param $request
     * @return array
     * @throws \Exception
     */
    public function newLogin($request)
    {
        $gameHistory = $request['game_history'] ?? [];
        $gameHistoryLog = $request['game_history_log'] ?? [];
        $keys = Arr::only($request, ['facebook_auth_code', 'google_auth_code', 'wechat_auth_code']);
        if ($keys = array_filter($keys)) {
            $user = self::finOneUser($keys);
            $request = array_merge($keys, [
                'device_uid' => $request['device_uid'] ?? '',
                'nick_name' => $request['nick_name'] ?? '',
                'profile_img' => $request['profile_img'] ?? '',
            ]);
            if (isset($request['facebook_auth_code']) && $request['facebook_auth_code']) {
                $request['reg_source'] = $this->model::REG_SOURCE_FB;
            } elseif (isset($request['google_auth_code']) && $request['google_auth_code']) {
                $request['reg_source'] = $this->model::REG_SOURCE_GOOGLE;
            } elseif (isset($request['wechat_auth_code']) && $request['wechat_auth_code']) {
                $request['reg_source'] = $this->model::REG_SOURCE_WECHAT;
            }
        }
        if (isset($request['phone']) && $request['phone']) {
            //验证码校验
            if (($request['verify_code'] ?? '') != Redis::GET(self::smsKey($request['area_code'] . $request['phone'], 'login'))) {
                throw new \Exception(transL('sms.sms_code_error'));
            }
            $user = $this->getUserByPhone($request['phone'], $request['area_code']);
            $request = Arr::except($request, ['facebook_auth_code', 'google_auth_code', 'wechat_auth_code']);
            $request['reg_source'] = $this->model::REG_SOURCE_PHONE;
        }

        if (isset($user) && $user) {
            if ($user['status'] == $this->model::STATUS_DISABLE) {
                throw new \Exception(transL('mer-user.account_disable'));
            }
        }

        if (empty($user)) {
            $data = $this->model->filter($request);
            $data['nick_name'] = isset($data['nick_name']) && $data['nick_name'] ? $data['nick_name'] : randomUser();
            $this->model->fill($data)->save();
            if (isset($request['tags']) && $request['tags']) {
                $this->model->tags()->sync($request['tags']);
            }
            $this->model->userInfo()->create([
                'referrer' => $request['referrer'] ?? '',
                'device_uid' => $request['device_uid'] ?? '',
            ]);
            $user = $this->model;
        }
        //同步历史游戏记录
        if ($gameHistory) {
            foreach ($gameHistory as $key => $gamePackageId) {
                MerUserGameLog::query()->updateOrCreate([
                    'mer_user_id' => $user->id,
                    'game_package_id' => $gamePackageId,
                ], [
                    'updated_at' => date('Y-m-d H:i:s', time() - 60 * $key)
                ]);
            }
        }
        //同步游戏时长数据
        if ($gameHistoryLog) {
            $gameHistoryModel = new MerUserGameHistory();
            $logList = $gameHistoryModel->newQuery()
                ->whereIn('uid', $gameHistoryLog)
                ->where('mer_user_id', 0)
                ->select('duration')
                ->pluck('duration')
                ->toArray();
            if ($logList) {
                $gameHistoryModel->newQuery()
                    ->whereIn('uid', $gameHistoryLog)
                    ->where('mer_user_id', 0)
                    ->update([
                        'mer_user_id' => $user->id
                    ]);

                MerUserInfo::query()->where('mer_user_id', $user->id)
                    ->increment('total_game_time', array_sum($logList));
            }
        }

        //生成token
        $user->token = 'Bearer ' . self::loginToken($user);
        $user['device_uid'] = $request['device_uid'] ?? '';

        $this->cacheToken($user);

        return $user->toArray();
    }


    /**
     * 重新登陆获取token
     * @param $user
     */
    protected function loginToken($user)
    {
//        $user->update([
//            'last_login_ip' => getClientIp(),
//            'last_login_date' => Carbon::now()->toDateTimeString()
//        ]);


        return auth()->login($user);
    }

    /**
     * @param $user
     */
    public function cacheToken($user)
    {
//        Redis::setex('auth_token_'.md5($user['token']),config('jwt.refresh_ttl')*60,json_encode($user));
        Redis::setex('auth_user_' . $user->id, config('jwt.refresh_ttl') * 60 + 30, json_encode($user));

        //ip地理获取
        $ip = getClientIp();
        $info = MerUserInfo::query()->where('mer_user_id', $user['id'])->first();
        if (empty($info['country_code'])) {
            if ($ipInfo = getIp2($ip)) {
                MerUserInfo::query()->where('mer_user_id', $user['id'])->update($ipInfo);
            }
        }
        return app(MerUserLoginLogService::class)->addLog($user, true);
    }

    /**
     * @param $token
     * @return bool
     */
    public function compareToken($token, $userId = 0)
    {
        $token = 'Bearer ' . $token;

//        $tokenUser = Redis::get('auth_token_'.md5($token));

//        $tokenUser = $tokenUser ? json_decode($tokenUser,true) : [];

//        if ($tokenUser) {
        $user = Redis::get('auth_user_' . $userId);

        $user = $user ? json_decode($user, true) : [];

        $result = md5($user['token']) == md5($token) ? true : false;
        if (!$result) {
            logger('token_compare_' . $userId . '_1:' . $user['token']);
            logger('token_compare_' . $userId . '_2:' . $token);
        }
        return $result;
//        }
//        return false;
    }

    /**
     *退出登陆
     */
    public function loginOut()
    {
        auth()->invalidate();
        return;
    }

    /**
     * 根据手机号查找用户
     * @param $phone
     * @param $code
     * @return mixed
     */
    public function getUserByPhone($phone, $code)
    {
        return $this->finOneUser([
            'phone' => $phone,
            'area_code' => $code,
        ]);
    }

    /**
     * 根据条件查找用户
     * @param $condition
     */
    public function finOneUser($condition)
    {
        return $this->model->newInstance()->buildQuery($condition)->first();
    }

    /**
     * 修改用户信息
     * @param $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function editUser($request)
    {
        $user = self::user();
        $thirdKeys = Arr::only($request, ['facebook_auth_code', 'google_auth_code', 'wechat_auth_code']);
        if ($thirdKeys) {
            sqlDump();
            $user = $this->model->newInstance()->buildQuery(array_filter($thirdKeys))->where('id', '!=', $user->id)->first();
            if ($user) {
                throw new \Exception(transL('mer-user.user_exist_from_third', '用户已存在'));
            }
        }
        if (isset($request['phone']) && $request['phone']) {
            //验证码校验
            if (($request['verify_code'] ?? '') != Redis::GET(self::smsKey($request['area_code'] . $request['phone'], 'bind'))) {
                throw new \Exception(transL('sms.sms_code_error'));
            }
            $user = $this->getUserByPhone($request['phone'], $request['area_code']);
            if ($user) {
                throw new \Exception(transL('mer-user.user_exist_from_third', '用户已存在'));
            }
        }
        foreach ($request as $k => $value) {
            if (in_array($k, [
                'last_login_ip',
                'last_login_date',
                'status',
                'vip',
                'vip_start_at',
                'vip_end_at',
                'verify_code'
            ])) {
                Arr::forget($request, $k);
            }
        }
        $request = $this->model->filter($request);
        $user->update($request);
        return $user;
    }

    /**
     * @param $request
     * @return int
     */
    public function editUserInfo($request)
    {
        $merUserInfo = new MerUserInfo();
        return $merUserInfo::query()->updateOrCreate([
            'mer_user_id' => $this->userId()
        ], $merUserInfo->filter($request));
    }

    /**
     * 用户信息
     * @param int $userId
     * @param bool $followData
     * @return \Illuminate\Database\Concerns\BuildsQueries|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function userInfo($userId = 0, $followData = true)
    {
        $loginUserId = $this->userId();
        $userId = $userId ? $userId : $loginUserId;
        $result = $this->model->query()
            ->select('id', 'profile_img', 'nick_name', 'description', 'sex', 'birth', 'area_code', 'phone', 'vip')
            ->where('id', $userId);
        if ($followData) {
            $result = $result->withCount(['follow', 'followed'])
                ->when($loginUserId && $userId != $loginUserId, function ($query) {
                    $query->with(['isUserFollow' => function ($query1) {
                        $query1->where('mer_user_id', $this->userId());
                    }]);
                });
        }
        if ($loginUserId && $userId == $this->userId()) {
            $result = $result->addSelect('facebook_auth_code', 'google_auth_code', 'wechat_auth_code')
                ->with(['userInfo' => function ($query) {
                    $query->select('mer_user_id', 'coins', 'first_wechat_bind', 'first_play_game', 'total_game_time');
                }]);
        }
        $result = $result->firstOrFail();
        if ($result['phone'] && empty($result['area_code'])) {
            $result['follow_count'] = rand(1000, 2000);
            $result['followed_count'] = rand(1000, 2000);
        }
//        if ($userId == $this->userId()) {
//            if (isset($result['userInfo']['first_play_game']) && $result['userInfo']['first_play_game']==0) {
//                //统计总游戏时长
//                $result['userInfo']['game_duration'] = MerUserGameHistory::query()->where('mer_user_id',$userId)->count('duration');
//                if ($result['userInfo']['game_duration'] > 60 * 40) {
//                    $result['userInfo']['first_play_game'] = 2;
//                    MerUserInfo::query()->where('mer_user_id',$userId)->update([
//                        'first_play_game' => 2
//                    ]);
//                }
//            }
//        }

        return $result;
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function isVip($userId = 0)
    {
        $user = $this->userInfo($userId, false);
        return $user['vip'];
    }
}
