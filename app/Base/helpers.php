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
function getHttpContent($method = 'GET', $url, $params = null, $headers = null, $timeout = 20)
{
    $client = new GuzzleHttp\Client();
    $guzzleParams = [];
    if ($params !== null) {
        if ($headers && in_array('application/x-www-form-urlencoded',array_values($headers))) {
            $guzzleParams['form_params'] = $params;
        }else{
            $guzzleParams[strtoupper($method) === 'GET' ? 'query' : 'json'] = $params;
        }
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
function postCurl($url, $data = '', $type = "GET")
{
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
    $encryStr = implode(',', $data);
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
    $encryStr = implode(',', $data);
    if ($sign != md5(base64_encode($encryStr))) {
        throw new \Exception('Signature error');
    }
    if ($time && $time < time() - 600) {
        throw new \Exception('Signature expired');
    }

    return true;
}

/**
 * @return string
 */
function randString()
{
    $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $rand = $code[rand(0, 25)]
        . strtoupper(dechex(date('m')))
        . date('d') . substr(time(), -5)
        . substr(microtime(), 2, 5)
        . sprintf('%02d', rand(0, 99));
    for (
        $a = md5($rand, true),
        $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
        $d = '',
        $f = 0;
        $f < 8;
        $g = ord($a[$f]),
        $d .= $s[($g ^ ord($a[$f + 8])) - $g & 0x1F],
        $f++
    ) ;
    return $code[rand(0, 25)] . strtolower($d) . date('Ymd');
}

function ip()
{
    //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        return \request()->getClientIp();
    }
    $res = preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches [0] : '';
    return $res;
}


/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function getClientIp($type = 0, $adv = true)
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

/**
 * 根据日期计算年龄
 * @param $birth
 * @return mixed
 */
function howOld($birth)
{
    list($birthYear, $birthMonth, $birthDay) = explode('-', $birth);
    list($currentYear, $currentMonth, $currentDay) = explode('-', date('Y-m-d'));

    $age = $currentYear - $birthYear - 1;
    if ($currentMonth > $birthMonth || $currentMonth == $birthMonth && $currentDay >= $birthDay) $age++;

    return $age;
}

/**
 * @param $phoneNumber
 * @return bool
 * @throws \AlibabaCloud\Client\Exception\ClientException
 */
function sendSms($phoneNumber, $areaCode, $varifyCode)
{
    //2021-02-19 更换国外短信第三方
//    if ($areaCode != '86') {
//        $data = getHttpContent('post','http://api2.nxcloud.com/api/sms/mtsend',[
//            'appkey' => 'nAAeFl7X',
//            'secretkey' => '9KuEK988',
//            'phone' => $areaCode.$phoneNumber,
//            'content' => 'Dear FunTouch User,Welcome to register our service,your verify code is '.$varifyCode
//        ],[
//            'Content-Type' => 'application/x-www-form-urlencoded'
//        ]);
//        $data = json_decode($data,true);
//        if (isset($data['code']) && $data['code'] == 0) {
//            return true;
//        }
//    }else{
        \AlibabaCloud\Client\AlibabaCloud::accessKeyClient(config('app.ali_access_keyid'), config('app.ali_access_secret'))
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
        $TemplateCode = $areaCode == 86 ? 'SMS_205123396' : 'SMS_208440010';

//        $phoneNumber = $areaCode == '86' ? $phoneNumber : $areaCode . $phoneNumber;

        $params = [
            'code' => $varifyCode
        ];

        if ($areaCode != '86') {
            $params['name'] = 'FunTouch User';
        }

        try {
            $result = \AlibabaCloud\Client\AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'PhoneNumbers' => $areaCode == '86' ? $phoneNumber : $areaCode . $phoneNumber,
                        'SignName' => "FunTouch",
                        'TemplateCode' => $TemplateCode,
                        'TemplateParam' => json_encode($params),
                    ],
                ])
                ->request();
        } catch (\AlibabaCloud\Client\Exception\ClientException $e) {
            throw new Exception($e->getErrorMessage());
        } catch (\AlibabaCloud\Client\Exception\ServerException $e) {
            throw new Exception($e->getErrorMessage());
        }
        $result = $result->toArray();
        if (isset($result['Message']) && $result['Message'] == 'OK') {
            $content = [
                'SMS_205123396' => '尊敬的用户，您的注册会员验证码为：{code}，该验证码5分钟内有效，请勿泄漏于他人！',
                'SMS_208440010' => 'Dear {name},Welcome to register our service,your verify code is {code}.'
            ];

            foreach ($params as $k => $v){
                $content = str_replace('{'.$k.'}',$v,$content);
            }
            \Illuminate\Support\Facades\DB::table('sys_sms_log')->insert([
                'area_code' => $areaCode,
                'phone' => $phoneNumber,
                'template_code' => $TemplateCode,
                'content' => $content[$TemplateCode],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return true;
        }
//    }

    throw new Exception($result['Message'] ?? transL('sms.send_error', '发送失败'));
}

/**
 * @return string
 */
function getLangField(string $field)
{
    $lang = config('app.locale');
    $lang = in_array($lang,[
        'zh-CN',
        'en',
    ]) ? $lang : 'en';

    return $lang == 'zh-CN' ? $field : ($field . '_' . strtolower($lang));
}

/**
 * @param $path
 * @return string
 */
function ossDomain($path)
{
    if ($path && !\Illuminate\Support\Str::startsWith($path, 'http://') && !\Illuminate\Support\Str::startsWith($path, 'https://')) {
        return config('filesystems.disks.oss.domain_url') . $path.(isImage($path) ? '?x-oss-process=style/yasuo' : '');
    }
    return $path;
}

/**
 * @param $filename
 * @return false|int
 */
function isImage($filename)
{
    $types = '.gif|.jpeg|.png|.bmp|.webp|.jpg'; //定义检查的图片类型
    if (!$info = pathinfo($filename)){
        return 0;
    }
    return stripos($types,$info['extension']);
}

/**
 * @param $path
 * @param false $crack
 * @return string
 */
function gameUrl($path,$crack=false)
{
    if (empty($path)) {
        return '';
    }
    $appName = $crack ? config('app.crack_game_url'):config('app.game_url');
    return (strpos($path, 'zip') !== false ? config('filesystems.disks.oss.domain_url') : $appName)
        . $path;
}

/**
 * @param string $str
 * @return array|string
 */
function findNum($str=''){
    $str=trim($str);
    if(empty($str)){return '';}
    $temp=array('1','2','3','4','5','6','7','8','9','0','.');
    $amount=$mark='';
    for($i=0;$i<strlen($str);$i++){
        if(in_array($str[$i],$temp)){
            $amount.=$str[$i];
        }else{
            $mark.=$str[$i];
        }
    }
    return compact('mark','amount');
}
/**
 * @return string
 */
function makeOrderNumber()
{
    $order_id_main = date('YmdHis') . rand(100000,999999);

    $order_id_len = strlen($order_id_main);

    $order_id_sum = 0;

    for($i=0; $i<$order_id_len; $i++){

        $order_id_sum += (int)(substr($order_id_main,$i,1));

    }

    $osn = $order_id_main . str_pad((100 - $order_id_sum % 100) % 100,2,'0',STR_PAD_LEFT);

    return $osn;
}

/**
 * @param $path
 * @return string
 */
function importImage($path)
{
    return config('filesystems.disks.oss.domain_url').'import/'.$path.'?x-oss-process=style/yasuo';
}

/**
 * @return string
 */
function randomUser()

{

    $male_names=array("James","John","Robert","Michael","William","David","Richard","Charles","Joseph","Thomas","Christopher","Daniel","Paul","Mark","Donald","George","Kenneth","Steven","Edward");

    $famale_names = array("Mary","Patricia","Linda","Barbara","Elizabeth","Jennifer","Maria","Susan","Margaret","Dorothy","Lisa","Nancy","Karen","Betty","Helen","Sandra","Donna","Carol","Ruth");

    $surnames =array("Smith","Jones","Taylor","Williams","Brown","Davies","Evans","Wilson","Thomas","Roberts","Johnson","Lewis","Walker","Robinson","Wood","Thompson","White","Watson","Jackson");

    $frist_num = mt_rand(0,19);

    $sur_num = mt_rand(0,19);

    $type = rand(0,1);

    if($type==0){

        $username=($male_names[$frist_num]??$male_names[0])." ".($surnames[$sur_num]??$surnames[0]);

    } else {

        $username=($famale_names[$frist_num]??$famale_names[0])." ".($surnames[$sur_num]??$surnames[0]);

    }

//    $lchar = 0;
//
//    $char = 0;
//
//    for($i = 0; $i < 4; $i++)
//
//    {
//
//        while($char == $lchar)
//
//        {
//
//            $char = rand(48, 109);
//
//            if($char > 57) $char += 7;
//
//            if($char > 90) $char += 6;
//
//        }
//
//        $user .= chr($char);
//
//        $lchar = $char;
//
//    }

    return $username;

}

/**
 * 获取ip地理位置
 * @param $ip
 * @throws \GeoIp2\Exception\AddressNotFoundException
 * @throws \MaxMind\Db\Reader\InvalidDatabaseException
 */
function getIp2($ip = null)
{
    try {
        $ip = $ip ? $ip : getClientIp();
        $reader = new \GeoIp2\Database\Reader(storage_path('GeoLite2-City.mmdb'));
        $record = $reader->city($ip);
        return [
            'country_code' => $record->country->isoCode,
            'country_name' => $record->country->name,
            'city_name' => $record->city->name,
            'latitude' => $record->location->latitude,
            'longitude' => $record->location->longitude,
        ];
    }catch (Exception $exception){

    }
    return [];
}

/**
 * 多维数组排序
 * @param $data
 * @param $sort_order_field
 * @param int $sort_order
 * @param int $sort_type
 * @return mixed
 */
function my_array_multisort($data,$sort_order_field,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC){
    foreach($data as $val){
        $key_arrays[]=$val[$sort_order_field];
    }
    array_multisort($key_arrays,SORT_ASC,SORT_NUMERIC,$data);
    return $data;
}

