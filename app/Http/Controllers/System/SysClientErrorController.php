<?php

namespace App\Http\Controllers\System;

use App\Services\System\SysClientErrorService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SysClientErrorController extends Controller
{
    protected $service;

    public function __construct(SysClientErrorService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return \App\Base\Services\BaseModel
     * @throws \GeoIp2\Exception\AddressNotFoundException
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'key' => 'required' ,
            'error_msg' => 'required' ,
        ]);
        return $this->service->store($request->all());
    }

}
