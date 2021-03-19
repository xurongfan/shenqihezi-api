<?php

namespace App\Http\Middleware;

use App\Services\MerUser\MerUserService;
use Auth;
use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class AuthToken extends BaseMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|mixed
     * @throws JWTException
     */
    public function handle($request, Closure $next)
    {
        // 检查此次请求中是否带有 token，如果没有则抛出异常。
        $this->checkForToken($request);
        $role = 'api';
        // 判断token是否在有效期内
        try {
            if (auth($role)->payload()) {
                if (!auth($role)->user()) {
                    throw new \Exception(transL('mer-user.login_expired', '登录失效,请重新登录.'), 401);
                }
                //单点登录
                $this->CheckSsoToken(auth('api')->getToken(), auth($role)->user()->id);
                app('auth')->shouldUse($role);
                return $next($request);
            }
        } catch (JWTException $exception) {
            try {
                $oldToken = auth('api')->getToken();

                $token = auth($role)->refresh();
                //使用一次性登录以保证此次请求的成功
                auth($role)->onceUsingId(
                    auth($role)->payload()->get('sub')
                );
                $user = auth($role)->user();
                $user['device_uid'] = $request->input('device_uid');

                //并发token过期处理
                $res = Redis::set('token_lock_' . $user->id, $token, 'nx', 'ex', 5);
                if ($res) {
                    $this->CheckSsoToken($oldToken, auth($role)->user()->id);

                    //更新请求中的token
                    $newToken = 'Bearer ' . $token;
                    $request->headers->set('Authorization', $newToken);
                    //更新登录时间

                    $user->update(
                        [
                            'last_login_ip' => getClientIp(),
                            'last_login_date' => Carbon::now()->toDateTimeString()
                        ]
                    );
                    $user->token = $newToken;

                    app(MerUserService::class)->cacheToken($user);

//                    //是同一个则删除锁
//                    if (Redis::get('token_lock_'.$user->id) == $token) {
                    Redis::del('token_lock_' . $user->id);
//                    }
                    // 在响应头中返回新的 token
                    return $this->setAuthenticationHeader($next($request), $token);
                } else {
                    if ($lockToken = Redis::get('token_lock_' . $user->id)) {
                        return $this->setAuthenticationHeader($next($request), $lockToken);
                    }
//                    $redisUser = Redis::get('auth_user_'.$user->id);

//                    $redisUser = $redisUser ? json_decode($redisUser,true) : [];
                }


            } catch (JWTException $exception) {
                // 如果捕获到此异常，即代表 refresh 也过期了，用户无法刷新令牌，需要重新登录。
                throw new \Exception(transL('mer-user.login_expired', '登录失效,请重新登录.'), 401);
//                throw new UnauthorizedHttpException('jwt-auth', $exception->getMessage());
            }
        }
        return $next($request);
    }

    /**
     * 单点登录
     * @throws \Exception
     */
    private function CheckSsoToken($token, $userId = 0)
    {
        if (app(MerUserService::class)->compareToken($token, $userId) == false) {
            throw new \Exception(transL('mer-user.need_relogin', 'Your account is logged in elsewhere.'), 401);
        }
    }

}
