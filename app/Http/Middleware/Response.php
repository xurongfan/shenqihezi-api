<?php

namespace App\Http\Middleware;

use App\Model\System\SysOperateLog;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class Response
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        if(strtolower($request->getMethod()) == 'options'){
//            $response = new \Illuminate\Http\Response();
//            $response->withHeaders([
//                'Content-Type'  => $request->expectsJson() || $request->ajax()?'application/json;charset:UTF-8':'text/html; charset=UTF-8',
//                'Access-Control-Allow-Origin'=>'*',
//                'Access-Control-Allow-Credentials'=>'true',
//                'Access-Control-Allow-Methods'=>'PUT, GET, POST, DELETE, OPTIONS',
//                'Access-Control-Allow-Headers' => 'Accept,Content-Type,Authorization,X-Requested-With,X-XSRF-TOKEN',
//                'Access-Control-Expose-Headers' => 'Authorization'
//            ]);
//            return $response;
//        }
        //去除请求参数左右两边空格
        $params = $request->all();
        foreach($params as $key=>$value){
            if(!is_array($value)) {
                $params[$key] = trim($value);
            }
        }
        $request->replace($params);
//        \DB::enableQueryLog();
        $response = $next($request);
//        print_r(\DB::getQueryLog());
        if ($response->getStatusCode()==200 && $response->exception == null) {
            $content = $response->getContent();
            if($request->expectsJson() || $request->ajax()) {
                $content = json_encode([
                    'code' => 0,
                    'msg' => 'success.',
                    'data' => ($this->isJson($content) ? json_decode($content, true) : $content)
                ]);
                $response->setContent($content)->withHeaders(['Content-Type' => 'application/json']);
            }
        }
        $this->saveAccessLog($request->method(),$request->path(),$params,$response);
//        $response->withHeaders([
//            'Content-Type'  =>  $request->expectsJson() || $request->ajax()?'application/json;charset:UTF-8':'text/html; charset=UTF-8',
//            'Access-Control-Allow-Origin'=>'*',
//            'Access-Control-Allow-Credentials'=>'true',
//            'Access-Control-Allow-Methods'=>'PUT, GET, POST, DELETE, OPTIONS',
//            'Access-Control-Allow-Headers' => 'Accept,Content-Type,Authorization,X-Requested-With,X-XSRF-TOKEN',
//            'Access-Control-Expose-Headers' => 'Authorization'
//        ]);
        return $response;
    }

    /**
     * @param $string
     * @return bool
     */
    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * 添加访问日志
     * @param $response
     */
    private function saveAccessLog($method,$route,$params,$response){
//        try{
            $user = auth()->guard('api')->user();
            $request = app()->make(Request::class);
            $routeUrl = $request->root().'/'.trim($route,'/');

            if(empty($user) && $routeUrl != route('auth.login')){
                return ;
            }

            $data = [
                'method' => strtolower($method),
                'route' => $request->root().'/'.trim($route,'/'),
                'params' => $params ? json_encode($params,JSON_UNESCAPED_UNICODE):'',
                'status_code' => $response->getStatusCode(),
                'response' => $response->getContent(),
                'error_code' => $response->exception ? $response->exception->getCode() : 0,
                'error_message' => $response->exception ? $response->exception->getMessage() : '',
                'user_id' => $user->id ?? '',
                'ip'        => getClientIp(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            SysOperateLog::query()->insert($data);

//        }catch(\Exception $e){
//
//        }
    }
}