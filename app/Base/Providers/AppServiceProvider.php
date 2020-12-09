<?php

namespace App\Base\Providers;

use App\Models\Game\GamePackage;
use App\Models\Game\GameTag;
use App\Models\Notice\Notice;
use App\Models\System\SysConfig;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicContent;
use App\Models\Topic\TopicContentComment;
use App\Models\Topic\TopicContentCommentLike;
use App\Models\Topic\TopicContentLike;
use App\Models\Topic\TopicUser;
use App\Models\User\MerUser;
use App\Models\User\MerUserFollow;
use App\Models\User\MerUserGameCollection;
use App\Models\User\MerUserGameHistory;
use App\Models\User\MerUserGameIntegral;
use App\Models\User\MerUserGameLike;
use App\Services\Game\GamePackageService;
use App\Services\Game\GameTagService;
use App\Services\MerUser\MerUserFollowService;
use App\Services\MerUser\MerUserGameCollectionService;
use App\Services\MerUser\MerUserGameHistoryService;
use App\Services\MerUser\MerUserGameIntegralService;
use App\Services\MerUser\MerUserGameLikeService;
use App\Services\MerUser\MerUserService;
use App\Services\Notice\NoticeService;
use App\Services\System\SysConfigService;
use App\Services\Topic\TopicContentCommentLikeService;
use App\Services\Topic\TopicContentCommentService;
use App\Services\Topic\TopicContentLikeService;
use App\Services\Topic\TopicContentService;
use App\Services\Topic\TopicService;
use App\Services\Topic\TopicUserService;
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

        $this->app->bind(TopicContentService::class, function () {
            return new TopicContentService(new TopicContent());
        });

        $this->app->bind(TopicService::class, function () {
            return new TopicService(new Topic());
        });

        $this->app->bind(TopicContentCommentService::class, function () {
            return new TopicContentCommentService(new TopicContentComment());
        });

        $this->app->bind(TopicContentCommentLikeService::class, function () {
            return new TopicContentCommentLikeService(new TopicContentCommentLike());
        });

        $this->app->bind(TopicUserService::class, function () {
            return new TopicUserService(new TopicUser());
        });

        $this->app->bind(TopicContentLikeService::class, function () {
            return new TopicContentLikeService(new TopicContentLike());
        });

        $this->app->bind(MerUserFollowService::class, function () {
            return new MerUserFollowService(new MerUserFollow());
        });

        $this->app->bind(NoticeService::class, function () {
            return new NoticeService(new Notice());
        });


    }
}
