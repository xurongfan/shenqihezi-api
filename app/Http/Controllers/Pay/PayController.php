<?php

namespace App\Http\Controllers\Pay;

use App\Services\Pay\PayProjectService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayController extends Controller
{
    /**
     * @return mixed
     */
    public function project(Request $request)
    {
        return app(PayProjectService::class)->index($request->is_google);
    }
}
