<?php

namespace App\Http\Controllers\MerUser;

use App\Services\MerUser\MerUserCoinsLogService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MerUserCoinsController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        return app(MerUserCoinsLogService::class)->index();
    }
}
