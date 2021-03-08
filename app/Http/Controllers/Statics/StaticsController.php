<?php

namespace App\Http\Controllers\Statics;

use App\Services\Statics\StaticsRemainService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StaticsController extends Controller
{
    public function remain()
    {
        return app(StaticsRemainService::class)->index();
    }
}
