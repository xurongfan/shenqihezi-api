<?php
use GuzzleHttp\Psr7\Request;
/**
 * 调试sql语句
 */
function sqlDump()
{
    \DB::listen(function ($query) {
        $i = 0;
        $rawSql = preg_replace_callback('/\?/', function ($matches) use ($query, &$i) {
            $item = isset($query->bindings[$i]) ? $query->bindings[$i] : $matches[0];
            $i++;
            return gettype($item) == 'string' ? "'$item'" : $item;
        }, $query->sql);
//        \Log::info($rawSql);
        echo $rawSql, "\n\n";
    });
}

/**
 * @param null $id
 * @param string $msg
 * @param array $replace
 * @return string
 */
function transL($id = null, $msg = '', $replace = [])
{
    $data = trans($id, $replace);
    if (is_array($data)) {
        $data = $data[1];
    }
    if ($replace) {
        foreach ($replace as $key => $item) {
            $data = str_replace('{' . $key . '}', $item, $data);
        }
    }
    return $data ?: $msg;
}

/**
 * @param string $method
 * @param $url
 * @param null $data
 * @param string $headers
 * @param int $timeout
 * @return mixed
 * @throws \GuzzleHttp\Exception\GuzzleException
 */
function getHttpContent($method = 'GET',$url, $params = null, $headers = null , $timeout = 20)
{
    $client = new GuzzleHttp\Client();
    $guzzleParams = [];
    if ($params !== null) {
        $guzzleParams[strtoupper($method) === 'GET' ? 'query' : 'json'] = $params;
    }

    if ($headers !== null) {
        $guzzleParams['headers'] = $headers;
    }
    $response = $client->request($method, $url, $guzzleParams);

    return (string)$response->getBody();
}

/**
 * @param $url
 * @return bool|string
 */
function postCurl($url, $data = '', $type = "GET") {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tmpInfo = curl_exec($ch);
    if (curl_errno($ch)) {
        return curl_error($ch);
    }
    curl_close($ch);
    return $tmpInfo;
}

/**
 * 生成url加密串
 * @param array $data
 * @return string
 * @throws Exception
 */
function encryption($data = [])
{
    if (empty($data)) {
        throw new \Exception('encry data empty');
    }
    ksort($data);
    $encryStr = implode(',',$data);
    $data['sign'] = md5(base64_encode($encryStr));
    return http_build_query($data);
}

/**
 * 校验签名
 * @param array $data
 * @return bool
 * @throws Exception
 */
function decryption($data = [])
{
    if (!isset($data['sign'])) {
        throw new \Exception('Lack of signature');
    }
    ksort($data);
    $sign = $data['sign'];
    $time = $data['time'] ?? 0;
    unset($data['sign']);
    $encryStr = implode(',',$data);
    if ($sign != md5(base64_encode($encryStr))) {
        throw new \Exception('Signature error');
    }
    if ($time && $time < time()-600) {
        throw new \Exception('Signature expired');
    }

    return true;
}

/**
 * @return string
 */
function randString() {
    $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $rand = $code[rand(0,25)]
        .strtoupper(dechex(date('m')))
        .date('d').substr(time(),-5)
        .substr(microtime(),2,5)
        .sprintf('%02d',rand(0,99));
    for(
        $a = md5( $rand, true ),
        $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
        $d = '',
        $f = 0;
        $f < 8;
        $g = ord( $a[ $f ] ),
        $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
        $f++
    );
    return  $code[rand(0,25)].strtolower($d).date('Ymd');
}

function ip() {
    //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }else{
        return \request()->getClientIp();
    }
    $res =  preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    return $res;
}

/**
 * @param $url
 * @param int $weight
 * @param int $height
 * @return string
 */
function tailoringImg($url , $weight = 128, $height = 128)
{
    return $url.'?x-oss-process=image/resize,w_'.$weight.',h_'.$height.',limit_0';
}


/*
  //白山云文件、图片上传，支持 image/png、image/jpeg、image/bmp、image/gif、application/pdf、application/vnd.android.package-archive
  $fileObject：文件对像，$request->file('file');
  $project：项目，xjd：现金贷项目，dc：贷超，项目中约定即可
  $fileName：auto：自动命名，其它：手动命名(段是完整的文件名)
  $bucket：oss中bucket桶名称
 */

function baiShanCloudUpload($fileObject, $project, $fileName = 'auto', $bucket = 'xiyou') {
    try {
        $cType = $fileObject->getMimeType();

        if ($cType != 'application/zlib' && $cType != 'image/png' && $cType != 'image/jpeg' && $cType != 'image/bmp' && $cType != 'image/gif' && $cType != 'application/pdf' && $cType != 'application/vnd.android.package-archive' && $cType != 'video/mp4') {
            return -2;
        }
        $fName = $fileName;
        if ($fName == 'auto') {
            if ($cType != 'application/zlib') {
                $fName = $fileObject->hashName();
            } else {
                $fName = md5($fName) . '.svga';
            }
        }

        $cli = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region' => 'us-east-1',
            'credentials' => [
                'key' => env('BAISHANCLOUD_ACCESS_KEY', ''),
                'secret' => env('BAISHANCLOUD_ACCESS_SECRET', ''),
            ],
            'endpoint' => env('BAISHANCLOUD_ENDPOINT', ''),
        ]);
        $resp = $cli->putObject([
            'ACL' => 'public-read',
            'Bucket' => env('BAISHANCLOUD_BUCKET', $bucket),
            'Key' => $project . '/' . date('Ymd') . '/' . $fName,
            'ContentType' => $cType,
            'SourceFile' => $fileObject->path(),
        ]);
    } catch (Exception $exception) {
        return $exception->getMessage();
    }

    if ($cType != 'application/zlib') {
        return $resp->get('ObjectURL');

    } else {
        return str_replace("http://","https://",$resp->get('ObjectURL'));
    }

}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function getClientIp($type = 0, $adv = false)
{
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL) return $ip[$type];
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}