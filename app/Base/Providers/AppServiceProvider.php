<?php

namespace App\Base\Providers;

use App\Models\User\MerUser;
use App\Services\MerUser\MerUserService;
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
    }
}
