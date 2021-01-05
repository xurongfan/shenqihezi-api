<?php

namespace App\Http\Controllers\MerUser;

use App\Base\Controllers\Controller;
use App\Models\Game\GamePackageSubscribe;
use App\Services\MerUser\MerUserService;
use Illuminate\Http\Request;

/**
 * 用户
 * Class MerUserController
 * @package App\Http\Controllers\MerUser
 */
class MerUserController extends Controller
{
    protected $service;

    /**
     * MerUserController constructor.
     * @param MerUserService $service
     */
    public function __construct(MerUserService $service)
    {
        $this->service = $service;
    }
//{
//'ar-DZ': "^(\+?213|0)(5|6|7)\d{8}$",
//'ar-SY': "^(!?(\+?963)|0)?9\d{8}$",
//'ar-SA': "^(!?(\+?966)|0)?5\d{8}$",
//'en-US': "^(\+?1)?[2-9]\d{2}[2-9](?!11)\d{6}$",
//'cs-CZ': "^(\+?420)? ?[1-9][0-9]{2} ?[0-9]{3} ?[0-9]{3}$",
//'de-DE': "^(\+?49[ \.\-])?([\(]{1}[0-9]{1,6}[\)])?([0-9 \.\-\/]{3,20})((x|ext|extension)[ ]?[0-9]{1,4})?$",
//'da-DK': "^(\+?45)?(\d{8})$",
//'el-GR': "^(\+?30)?(69\d{8})$",
//'en-AU': "^(\+?61|0)4\d{8}$",
//'en-GB': "^(\+?44|0)7\d{9}$",
//'en-HK': "^(\+?852\-?)?[569]\d{3}\-?\d{4}$",
//'en-IN': "^(\+?91|0)?[789]\d{9}$",
//'en-NZ': "^(\+?64|0)2\d{7,9}$",
//'en-ZA': "^(\+?27|0)\d{9}$",
//'en-ZM': "^(\+?26)?09[567]\d{7}$",
//'es-ES': "^(\+?34)?(6\d{1}|7[1234])\d{7}$",
//'fi-FI': "^(\+?358|0)\s?(4(0|1|2|4|5)?|50)\s?(\d\s?){4,8}\d$",
//'fr-FR': "^(\+?33|0)[67]\d{8}$",
//'he-IL': "^(\+972|0)([23489]|5[0248]|77)[1-9]\d{6}",
//'hu-HU': "^(\+?36)(20|30|70)\d{7}$",
//'it-IT': "^(\+?39)?\s?3\d{2} ?\d{6,7}$",
//'ja-JP': "^(\+?81|0)\d{1,4}[ \-]?\d{1,4}[ \-]?\d{4}$",
//'ms-MY': "^(\+?6?01){1}(([145]{1}(\-|\s)?\d{7,8})|([236789]{1}(\s|\-)?\d{7}))$",
//'nb-NO': "^(\+?47)?[49]\d{7}$",
//'nl-BE': "^(\+?32|0)4?\d{8}$",
//'nn-NO': "^(\+?47)?[49]\d{7}$",
//'pl-PL': "^(\+?48)? ?[5-8]\d ?\d{3} ?\d{2} ?\d{2}$",
//'pt-BR': "^(\+?55|0)\-?[1-9]{2}\-?[2-9]{1}\d{3,4}\-?\d{4}$",
//'pt-PT': "^(\+?351)?9[1236]\d{7}$",
//'ru-RU': "^(\+?7|8)?9\d{9}$",
//'sr-RS': "^(\+3816|06)[- \d]{5,9}$",
//'tr-TR': "^(\+?90|0)?5\d{9}$",
//'vi-VN': "^(\+?84|0)?((1(2([0-9])|6([2-9])|88|99))|(9((?!5)[0-9])))([0-9]{7})$",
//'zh-CN': "^(\+?0?86\-?)?1[345789]\d{9}$",
//'zh-TW': "^(\+?886\-?|0)?9\d{8}$"
//}
    public function reg(Request $request)
    {
        logger('Reg Data:'.json_encode($request->all()));
        $this->validate($request,[
            'phone' => 'required' ,
            'area_code' => 'required',
            'verify_code' => 'required',
            'nick_name' => 'required',
            'sex' => 'required',
            'birth' => 'required',
            'tags' => 'required|array',
        ],[
            'phone.required' => transL('mer-user.phone_error'),
            'area_code.required' => transL('mer-user.area_code_error'),
            'verify_code.required' => transL('mer-user.verify_code_error'),
            'nick_name.required' => transL('mer-user.nick_name_error'),
            'sex.required' => transL('mer-user.sex_error'),
            'tags.required' => transL('mer-user.tags_error'),
        ]);

       return $this->service->reg($request->all());
    }

    /**
     * 登录
     * @return mixed
     */
    public function login(Request $request)
    {
        $this->validate($request,[
            'phone' => 'required_with:area_code,verify_code' ,
            'area_code' => 'required_with:phone,verify_code',
            'verify_code' => 'required_with:phone,area_code',
//            'facebook_auth_code' => 'required',
//            'google_auth_code' => 'required',
        ],[
            'phone.required_with' => transL('mer-user.phone_error'),
            'area_code.required_with' => transL('mer-user.area_code_error'),
            'verify_code.required_with' => transL('mer-user.verify_code_error'),
        ]);
        return  $this->service->login($request->all());
    }

    /**
     * 获取用户信息
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function info()
    {
        return $this->service->userInfo();
    }

    /**
     * 退出登陆
     * @return mixed
     */
    public function out(){
        return $this->service->loginOut();
    }

    /**
     * 发送短信
     * @param Request $request
     * @throws \AlibabaCloud\Client\Exception\ClientException
     */
    public function sendSms(Request $request)
    {
        $this->validate($request,[
            'phone' => 'required' ,
            'area_code' => 'required',
        ],[
            'phone.required' => transL('mer-user.phone_error'),
            'area_code.required' => transL('mer-user.area_code_error')
        ]);
        $type = \request('type','login');
        $phone = \request('phone');
        $areaCode = \request('area_code');

        return $this->service->sendSmsCode($phone,$areaCode,$type);
    }

    /**
     * 修改信息
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function edit()
    {
        return $this->service->editUser(\request()->all());
    }

    /**
     * 查询用户信息
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function user($id)
    {
        return $this->service->userInfo($id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     * @throws \Google\Exception
     */
    public function pay()
    {
        $requestData = request()->all();
        try {
            $response = getHttpContent('post','http://47.242.85.154:81/api/google-purchases',$requestData);
            $response = json_decode($response,true);
            $response['status'] = $response['status'] ?? 0;
            $response['good_type'] = $response['good_type'] ?? 1;
            $user = auth()->user();

            if ($response['status'] == 1) {
                $response['request_data'] = json_encode($response['request_data']);
                $order = \App\Models\Pay\PayOrder::query()->firstOrCreate(
                    [
                        'order_num' => md5($requestData['purchaseToken'])
                    ],
                    ['mer_user_id' => $user->id] + $response
                );
                if ($response['good_type'] == 1) {
                    //修改用户信息
                    $user->update([
                        'vip' => 1,
                        'vip_start_at' => date('Y-m-d H:i:s',time()),
//                    'vip_end_at' =>  date('Y-m-d H:i:s',$response->getExpiryTimeMillis()/1000),
                    ]);
                }else{
                    if (!str_is('funtouch_sp_*', $requestData['productId'])){
                        throw new \Exception('Product Id Error.');
                    }
                    $day = intval(str_replace('funtouch_sp_','',$requestData['productId'])) ;
                    if ($day == 0) {
                        throw new \Exception('Order Days Error.');
                    }
                    //游戏订阅时长
                    //funtouch_sp_3#funtouch_sp_7#funtouch_sp_30
//                    $requestData['productId']
                    $recharge = GamePackageSubscribe::query()
                        ->where('game_package_id',$requestData['game_package_id']??0)
                        ->where('mer_user_id',$user->id)
                        ->where('end_at','>',date('Y-m-d H:i:s'))
                        ->first();
                    if ($recharge) {
                        $recharge->update([
                            'end_at' => date('Y-m-d H:i:s',strtotime("+{$day}days",strtotime($recharge['end_at'])))
                        ]);
                    }else{
                        GamePackageSubscribe::query()->create([
                            'game_package_id' => $requestData['game_package_id']??0,
                            'mer_user_id' => $user->id,
                            'start_at' => date('Y-m-d H:i:s'),
                            'end_at' => date('Y-m-d 00:00:00',strtotime("+{$day}days")),
                        ]);
                    }

                }

                return $order;
            }

        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
        throw new \Exception(transL('common.order_not_pay'));
    }
}
