<?php

namespace App\Providers;

use App\Model\User\AdminUser;
use App\Model\Push\PushArticle;
use App\Model\Push\PushResult;
use App\Services\User\AdminUserService;
use App\Services\Push\PushArticleService;
use App\Services\Push\PushResultService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AdminUserService::class, function () {
            return new AdminUserService(new AdminUser());
        });

        $this->app->bind(PushArticleService::class, function () {
            return new PushArticleService(new PushArticle());
        });

        $this->app->bind(PushResultService::class, function () {
            return new PushResultService(new PushResult());
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
