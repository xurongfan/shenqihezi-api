<?php

namespace App\Services\Pay;

use App\Base\Services\BaseService;

class PayProjectService extends BaseService
{
    /**
     * @param int $isGoogle
     * @return array
     */
    public function index($isGoogle = 0)
    {
        return $this->model->query()->select('id','title','days','amount','google_pay_id')->when($isGoogle,function ($query){
            $query->where('google_pay_id','!=','');
        },function ($query){
            $query->where('google_pay_id','');
        })->orderBy('days','desc')
        ->get()
        ->toArray();
    }
}