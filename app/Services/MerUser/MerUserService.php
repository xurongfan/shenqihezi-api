<?php

namespace App\Services\MerUser;

use App\Base\Services\BaseService;
use App\Models\User\MerUserInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
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
    public function sendSmsCode( $phone , $areaCode = '',$type = 'login' )
    {

        $keys = request()->only('facebook_auth_code','google_auth_code','wechat_auth_code');
        if ($keys) {
            if (self::finOneUser($keys)){
                throw new \Exception(transL('mer-user.user_exist_from_third','用户已存在'));
            }

            if ($user = $this->getUserByPhone($phone,$areaCode)) {
                foreach ($keys as $k => $key){
                    if (isset($user[$k]) && $user[$k]){
                        throw new \Exception(transL('mer-user.user_exist_from_third','用户已存在'));
                    }
                }
            }
        }

        $varifyCode = mt_rand(1000,9999);
        if (sendSms($phone,$areaCode,$varifyCode)) {
            Redis::SETEX(self::smsKey($areaCode.$phone,$type),300,$varifyCode);
            return ;
        }
    }


    /**
     * @param string $type
     * @param $phoneNumber
     * @return string
     */
    public function smsKey($phoneNumber, $type = 'login' )
    {
        return $type.'_'.$phoneNumber;
    }

    /**
     * 注册
     * @param $request
     * @return \App\Base\Models\BaseModel|\App\Base\Services\BaseModel
     */
    public function reg($request)
    {
        if ($this->getUserByPhone($request['phone'],$request['area_code'])) {
            throw new \Exception(transL('mer-user.user_exist','用户已存在'));
        }
        //验证码校验
        if (($request['verify_code'] ?? '') != Redis::GET(self::smsKey($request['area_code'].$request['phone'],'login'))) {
            throw new \Exception(transL('sms.sms_code_error'));
        }
        $keys = Arr::only($request, ['facebook_auth_code', 'google_auth_code','wechat_auth_code']);
        if ($keys && self::finOneUser(array_filter($keys))){
            throw new \Exception(transL('mer-user.user_exist_from_third','用户已存在'));
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
        $this->model->token = 'Bearer '.self::loginToken($this->model);

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
            if (($request['verify_code'] ?? '') != Redis::GET(self::smsKey($request['area_code'].$request['phone'],'login'))) {
                throw new \Exception(transL('sms.sms_code_error'));
            }
            $user = $this->getUserByPhone($request['phone'],$request['area_code']);
            if (!isset($user)) {
                throw new \Exception(transL('mer-user.user_not_exist'),100);
            }
            //检查第三方key是否已被注册
            $keys = array_filter(Arr::only($request, ['facebook_auth_code', 'google_auth_code','wechat_auth_code']));
            foreach ($keys as $k => $key){
                if (isset($user[$k]) && $user[$k]){
                    throw new \Exception(transL('mer-user.user_exist_from_third','用户已存在'));
                }
            }
            if ($keys && self::finOneUser($keys)){
                throw new \Exception(transL('mer-user.user_exist_from_third','用户已存在'));
            }

            $user->update($keys);
        } else if (isset($request['facebook_auth_code']) && $request['facebook_auth_code']) {
            $user = $this->finOneUser(['facebook_auth_code' => $request['facebook_auth_code']]);
            if (!isset($user)) {
                throw new \Exception(transL('mer-user.third_login_user_not_exist'),100);
            }
        }else if (isset($request['google_auth_code']) && $request['google_auth_code']) {
            $user = $this->finOneUser(['google_auth_code' => $request['google_auth_code']]);
            if (!isset($user)) {
                throw new \Exception(transL('mer-user.third_login_user_not_exist'),100);
            }
        }else if (isset($request['wechat_auth_code']) && $request['wechat_auth_code']) {
            $user = $this->finOneUser(['wechat_auth_code' => $request['wechat_auth_code']]);
            if (!isset($user)) {
                throw new \Exception(transL('mer-user.third_login_user_not_exist'),100);
            }
        }
        $user->token =  'Bearer '.self::loginToken($user);
        $this->cacheToken($user);


        return $user;
    }


    /**
     * 重新登陆获取token
     * @param $user
     */
    protected function loginToken($user)
    {
        $user->update([
            'last_login_ip' => request()->getClientIp(),
            'last_login_date' => Carbon::now()->toDateTimeString()
        ]);
        return auth()->login($user);
    }

    /**
     * @param $user
     */
    public function cacheToken($user)
    {
//        Redis::setex('auth_token_'.md5($user['token']),config('jwt.refresh_ttl')*60,json_encode($user));
        Redis::setex('auth_user_'.$user->id,config('jwt.refresh_ttl')*60+30,json_encode($user));

    }

    /**
     * @param $token
     * @return bool
     */
    public function compareToken($token,$userId = 0)
    {
        $token = 'Bearer '.$token;

//        $tokenUser = Redis::get('auth_token_'.md5($token));

//        $tokenUser = $tokenUser ? json_decode($tokenUser,true) : [];

//        if ($tokenUser) {
            $user = Redis::get('auth_user_'.$userId);

            $user = $user ? json_decode($user,true) : [];

            return $user['token'] == $token ? true : false;
//        }
        return false;
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
    public function getUserByPhone($phone,$code)
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
        $user->update($this->model->filter($request));
        return $user;
    }

    /**
     * @param $request
     * @return int
     */
    public function editUserInfo($request)
    {
        return MerUserInfo::query()->updateOrCreate([
            'mer_user_id'=>$this->userId()
        ],$request);
    }

    /**
     * 用户信息
     * @param int $userId
     * @param bool $followData
     * @return \Illuminate\Database\Concerns\BuildsQueries|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function userInfo($userId = 0,$followData = true)
    {
        $userId = $userId ? $userId : $this->userId();
        $result = $this->model->query()
            ->select('id','profile_img','nick_name','description','sex','birth','area_code','phone','vip')
            ->where('id',$userId);
        if ($followData) {
            $result = $result->withCount(['follow','followed'])
                ->when($userId != $this->userId(),function ($query){
                    $query->with(['isUserFollow' => function($query1){
                        $query1->where('mer_user_id',$this->userId());
                    }]);
                });
        }
        if ($userId == $this->userId()) {
            $result = $result->addSelect('facebook_auth_code','google_auth_code','wechat_auth_code')
                        ->with(['userInfo' => function($query){
                            $query->select('mer_user_id','coins','first_wechat_bind','first_play_game');
                        }]);
        }
        return $result->firstOrFail();
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function isVip($userId = 0)
    {
        $user = $this->userInfo($userId,false);
        return $user['vip'];
    }
}
