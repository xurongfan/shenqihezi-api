<?php

namespace App\Base\Providers;

use App\Models\Game\GamePackage;
use App\Models\Game\GameTag;
use App\Models\System\SysConfig;
use App\Models\User\MerUser;
use App\Models\User\MerUserGameCollection;
use App\Models\User\MerUserGameHistory;
use App\Models\User\MerUserGameIntegral;
use App\Models\User\MerUserGameLike;
use App\Services\Game\GamePackageService;
use App\Services\Game\GameTagService;
use App\Services\MerUser\MerUserGameCollectionService;
use App\Services\MerUser\MerUserGameHistoryService;
use App\Services\MerUser\MerUserGameIntegralService;
use App\Services\MerUser\MerUserGameLikeService;
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

        $this->app->bind(MerUserGameCollectionService::class, function () {
            return new MerUserGameCollectionService(new MerUserGameCollection());
        });

        $this->app->bind(MerUserGameHistoryService::class, function () {
            return new MerUserGameHistoryService(new MerUserGameHistory());
        });

        $this->app->bind(MerUserGameLikeService::class, function () {
            return new MerUserGameLikeService(new MerUserGameLike());
        });

        $this->app->bind(MerUserGameIntegralService::class, function () {
            return new MerUserGameIntegralService(new MerUserGameIntegral());
        });

        $this->app->bind(GameTagService::class, function () {
            return new GameTagService(new GameTag());
        });

        $this->app->bind(GamePackageService::class, function () {
            return new GamePackageService(new GamePackage());
        });

        $this->app->bind(SysConfigService::class, function () {
            return new SysConfigService(new SysConfig());
        });
    }
}
