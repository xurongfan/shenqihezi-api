<?php

namespace App\Http\Controllers\Channel;

use App\Services\Channel\ChannelGoogleService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChannelController extends Controller
{
    /**
     * @return mixed
     */
    public function userChannel()
    {
        return app(ChannelGoogleService::class)->userChannel();
    }
}
