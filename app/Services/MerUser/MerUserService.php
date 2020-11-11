<?php

namespace App\Services\MerUser;

use App\Base\Services\BaseService;
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
//        if ($type == 'reg' && self::getUserByPhone($phone , $areaCode)) {
//            throw new \Exception(transL('mer-user.user_exist'));
//        }
//
//        if ($type == 'login' && !self::getUserByPhone($phone , $areaCode)) {
//            throw new \Exception(transL('mer-user.user_not_exist'));
//        }
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
     * @param $request
     * @return \App\Base\Models\BaseModel|\App\Base\Services\BaseModel
     */
    public function reg($request)
    {
        if ($this->getUserByPhone($request['phone'],$request['area_code'])) {
            throw new \Exception(transL('mer-user.user_exist','用户已存在'));
        }
        //验证码校验
        if ($request['verify_code'] ?? '' != Redis::GET(self::smsKey($request['area_code'].$request['phone'],'login'))) {
            throw new \Exception(transL('sms.sms_code_error'));
        }

        $data = $this->model->filter($request);

        $data['last_login_ip'] = request()->getClientIp();
        $data['last_login_date'] = Carbon::now()->toDateString();

        $this->model->fill($data)->save();
        $this->model->tags()->sync($request['tags']);
        //生成token
        $this->model->token = 'Bearer '.auth()->login($this->model);

        return $this->model;
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
        }

        $user->token =  'Bearer '.auth()->login($user);
        return $user;
    }


    /**
     * 重新登陆获取token
     * @param $user
     */
    protected function loginToken($user)
    {
        return auth()->login($user);
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
        $user->update($request);
        return $user;
    }
}