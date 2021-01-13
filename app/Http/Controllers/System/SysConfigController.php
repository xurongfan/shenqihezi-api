<?php

namespace App\Http\Controllers\System;

use App\Services\System\SysConfigService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SysConfigController extends Controller
{
    protected $service;

    public function __construct(SysConfigService $service)
    {
        $this->service = $service;
    }

    /**
     * @return \App\Base\Services\BaseModel
     */
    public function config(Request $request)
    {
        $this->validate($request,[
            'keyword' => 'required' ,
        ]);
        $result = $this->service->findOneBy([
            'keyword' => getLangField(\request('keyword'))
        ],'content');
        if (isset($result['content']) && $result['content'] && in_array(\request('keyword'),['report'])) {
            $result['content'] = json_decode($result['content'],true);
        }

        return $result;
    }
}
