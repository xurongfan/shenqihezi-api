<?php

namespace App\Http\Controllers\System;

use App\Services\System\SysFeedBackService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SysFeedBackController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'content' => 'required' ,
        ]);
        return app(SysFeedBackService::class)->store($request->all());
    }
}
