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
        $keyword = \request('keyword');
        $result = $this->service->findOneBy([
            'keyword' => in_array($keyword,[
                'privacy_policy',
                'help_support',
                'about_us',
                'report',
                'term_of_service',
            ]) ? getLangField($keyword) : $keyword
        ],'content');
        if (isset($result['content']) && $result['content'] && in_array($keyword,['report'])) {
            $result['content'] = json_decode($result['content'],true);
        }

        return $result;
    }

    /**
     * @param $key
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function viewConfig($key)
    {
        if ($lang = \request()->get('lang')){
            config([
                'app.locale' => $lang
            ]);
        }
        $result = $this->service->findOneBy([
            'keyword' => getLangField($key)
        ],'content');


        return view('paperwork',$result);
    }
}
