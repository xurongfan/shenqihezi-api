<?php


namespace App\Services\Pay;


use App\Models\Game\GamePackageSubscribe;
use App\Models\Pay\PayOrder;
use App\Models\User\MerUser;
use App\Services\Ali\AliService;
use App\Services\Wechat\WechatService;

class PayService
{
    /**
     * @param int $payType
     * @param int $projectId
     * @param int $gamePackageId
     * @return mixed
     * @throws \Exception
     */
    public function pay($payType = 0,$projectId = 0,$gamePackageId = 0)
    {
        $project = app(PayProjectService::class)->findOneBy(
            [
                'google_pay_id' => '',
                'id' => $projectId
            ]
        );
       if (empty($project)) {
           throw new \Exception(transL('common.pay_project_error'));
       }
       //生成订单信息
        $order = PayOrder::query()->create([
            'mer_user_id' => auth()->user()->id,
            'order_num' => makeOrderNumber(),
            'currency_code' => 'cny',
            'pay_project_id' => $projectId,
            'amount' => $project['amount'],
            'pay_type' => $payType,
            'good_type' => $project['is_vip'] ? 1 :2,
            'game_package_id' => $gamePackageId ? $gamePackageId : 0,
            'desc' => $project['title']
        ]);

        $result = $payType == PayOrder::PAY_TYPE_WECHAT ?
            app(WechatService::class)->pay($order)
            :
            app(AliService::class)->pay($order);

        return $result;
    }

    /**
     * @param $orderNum
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function notifyEvent($orderNum)
    {
        $order = PayOrder::query()->where('order_num',$orderNum)->with('project')->firstOrFail();
        if ($order->status == 0){
            //已支付状态
            $order->status = 1;
            $order->pay_time = date('Y-m-d H:i:s');

            //vip
            if ($order->good_type == 1){


                //修改用户信息
                $user = MerUser::query()->where('id',$order->mer_user_id)->firstOrFail();

                if ($user->vip == 1){
                   if ( $user['vip_end_at'] && strtotime( $user['vip_end_at']) < time()){
                       $vipStartAt = date('Y-m-d H:i:s',time());
                       $vipEndAt =  date('Y-m-d 00:00:00',strtotime("+{$order['project']['days']}days"));
                   }else{
                       $vipStartAt = $user['vip_start_at'];
                       $vipEndAt =   date('Y-m-d H:i:s',strtotime("+{$order['project']['days']}days",strtotime($user['vip_end_at'])));
                   }
                }else{
                    $vipStartAt = date('Y-m-d H:i:s',time());
                    $vipEndAt =  date('Y-m-d 00:00:00',strtotime("+{$order['project']['days']}days"));
                }
                $user->update([
                    'vip' => 1,
                    'vip_start_at' => $vipStartAt,
                    'vip_end_at' =>  $vipEndAt,
                ]);


            }else{
                //订阅游戏
                $recharge = GamePackageSubscribe::query()
                    ->where('game_package_id',$order->game_package_id)
                    ->where('mer_user_id',$order->mer_user_id)
                    ->where('end_at','>',date('Y-m-d H:i:s'))
                    ->first();
                if ($recharge) {

                    $recharge->update([
                        'end_at' => date('Y-m-d H:i:s',strtotime("+{$order['project']['days']}days",strtotime($recharge['end_at'])))
                    ]);
                }else{
                    GamePackageSubscribe::query()->create([
                        'game_package_id' => $order->game_package_id,
                        'mer_user_id' => $order->mer_user_id,
                        'start_at' => date('Y-m-d H:i:s'),
                        'end_at' => date('Y-m-d 00:00:00',strtotime("+{$order['project']['days']}days")),
                    ]);
                }

            }
            $order->save();
        }

        return $order;
    }
}