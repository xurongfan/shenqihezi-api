<?php

namespace App\Http\Middleware;

use Closure;

class LaraRsa
{

    protected $timeOut = 30;

    protected $priKey = null;

    protected $pubKey = null;


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->priKey = file_get_contents(config("lararsa.private_key_file", ""));
        $this->pubKey = file_get_contents(config("lararsa.public_key_file", ""));
        // 需要开启openssl扩展
        if (!extension_loaded("openssl")) {
            throw new \Exception("RSA Error:Please open the openssl extension first",500);
        }

        $data = $request->input('data',null);
        $result = self::decrypt($data);
        if (empty($result)){
            throw new \Exception('params empty.');
        }
        $result = json_decode($result,true);
        //客户端获取的是设备当前时间
//        if (isset($result['millis']) && (time() - $result['millis'] >= $this->timeOut)){
//            throw new \Exception('timeout error.');
//        }
        $request->replace($result);
        return $next($request);
    }

    /**
     * @param $encryptData
     * @return string
     */
    private function decrypt($encryptData){
        $crypto = '';
        foreach (str_split(base64_decode($encryptData), 128) as $chunk) {
            openssl_private_decrypt($chunk, $decryptData, $this->priKey);
            $crypto .= $decryptData;
        }
        return $crypto;
    }

    /**
     * @param $originalData
     * @return string
     */
    private function encrypt($originalData){
        $crypto = '';
        foreach (str_split($originalData, 117) as $chunk) {
            openssl_public_encrypt($chunk, $encryptData, $this->pubKey);
            $crypto .= $encryptData;
        }
        return base64_encode($crypto);
    }
}
