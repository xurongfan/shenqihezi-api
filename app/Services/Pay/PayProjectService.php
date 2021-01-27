<?php

namespace App\Services\Pay;

use App\Base\Services\BaseService;

class PayProjectService extends BaseService
{
    /**
     * @param int $isGoogle
     * @param int $isVip
     * @return array
     */
    public function index($isGoogle = 0,$isVip = 0)
    {
        return $this->model->query()->select('id','title','days','amount','google_pay_id')->when($isGoogle,function ($query){
            $query->where('google_pay_id','!=','');
        },function ($query){
            $query->where('google_pay_id','');
        })
        ->where('is_vip',$isVip ?? 0)
        ->orderBy('days','desc')
        ->get()
        ->toArray();
    }
}