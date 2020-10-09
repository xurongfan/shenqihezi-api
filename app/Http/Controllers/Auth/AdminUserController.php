<?php
/**
 * Created by PhpStorm.
 * User: xurf
 * Date: 2020/8/26
 * Time: 15:47
 */

namespace App\Http\Controllers\Auth;


use App\Base\Controllers\Controller;
use App\Services\User\AdminUserService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminUserController extends Controller
{
    protected $service;

    /**
     * AdminUserController constructor.
     * @param AdminUserService $service
     */
    public function __construct(AdminUserService $service)
    {
        $this->service = $service;
    }

    /**
     * 登录
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
            'captcha' => 'required|captcha_api:' . $request->input('ckey','')
        ]);

        $credentials = $request->only('username', 'password');

        $jwt_token = null;
        if (!$jwt_token = JWTAuth::attempt($credentials)) {
            throw new \Exception('用户名密码错误或已被禁用');
        }
        return [
            'token' => $jwt_token,
            'token_type' => 'Bearer ',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ];
    }

    /**
     * 退出登陆
     * @param Request $request
     */
    public function logout(Request $request)
    {
        JWTAuth::parseToken()->invalidate();
        return ;
    }

    /**
     * 获取用户信息
     * @return mixed
     */
    public function user()
    {
        return JWTAuth::parseToken()->touser();
    }
}