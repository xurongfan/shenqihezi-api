<?php

namespace App\Base\Providers;

use App\Models\Game\GameTag;
use App\Models\System\SysConfig;
use App\Models\User\MerUser;
use App\Services\Game\GameTagService;
use App\Services\MerUser\MerUserService;
use App\Services\System\SysConfigService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * 注册
     */
    public function register()
    {
        $this->app->bind(MerUserService::class, function () {
            return new MerUserService(new MerUser());
        });

        $this->app->bind(GameTagService::class, function () {
            return new GameTagService(new GameTag());
        });

        $this->app->bind(SysConfigService::class, function () {
            return new SysConfigService(new SysConfig());
        });
    }
}
