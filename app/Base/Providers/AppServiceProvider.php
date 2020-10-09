<?php

namespace App\Base\Providers;

use App\Models\Feedback;
use App\Models\Message;
use App\Models\Program;
use App\Models\Shop;
use App\Services\Apps\InstallLogService;
use App\Services\Apps\MessageService;
use App\Services\Apps\ProgramService;
use App\Services\Apps\ShopService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * 注册
     */
    public function register()
    {

    }
}
