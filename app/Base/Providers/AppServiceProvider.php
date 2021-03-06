<?php

namespace App\Base\Providers;

use App\Models\Channel\ChannelGoogle;
use App\Models\Game\AdBurying;
use App\Models\Game\GamePackage;
use App\Models\Game\GamePackageSubscribe;
use App\Models\Game\GameTag;
use App\Models\Pay\PayProject;
use App\Models\System\SysFeedBack;
use App\Models\Topic\TopicContentResource;
use App\Models\Topic\TopicContentUserShield;
use App\Models\User\MerRetainLog;
use App\Models\Notice\Notice;
use App\Models\System\SysConfig;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicContent;
use App\Models\Topic\TopicContentComment;
use App\Models\Topic\TopicContentCommentLike;
use App\Models\Topic\TopicContentLike;
use App\Models\Topic\TopicContentReport;
use App\Models\Topic\TopicUser;
use App\Models\User\MerUser;
use App\Models\User\MerUserCoinsLog;
use App\Models\User\MerUserFollow;
use App\Models\User\MerUserGameCollection;
use App\Models\User\MerUserGameHistory;
use App\Models\User\MerUserGameIntegral;
use App\Models\User\MerUserGameLike;
use App\Services\Channel\ChannelGoogleService;
use App\Services\Game\AdBuryingService;
use App\Services\Game\GamePackageService;
use App\Services\Game\GamePackageSubscribeService;
use App\Services\Game\GameTagService;
use App\Services\MerUser\MerRetainLogService;
use App\Services\MerUser\MerUserCoinsLogService;
use App\Services\MerUser\MerUserFollowService;
use App\Services\MerUser\MerUserGameCollectionService;
use App\Services\MerUser\MerUserGameHistoryService;
use App\Services\MerUser\MerUserGameIntegralService;
use App\Services\MerUser\MerUserGameLikeService;
use App\Services\MerUser\MerUserService;
use App\Services\Notice\NoticeService;
use App\Services\Pay\PayProjectService;
use App\Services\System\SysConfigService;
use App\Services\System\SysFeedBackService;
use App\Services\Topic\TopicContentCommentLikeService;
use App\Services\Topic\TopicContentCommentService;
use App\Services\Topic\TopicContentLikeService;
use App\Services\Topic\TopicContentReportService;
use App\Services\Topic\TopicContentResourceService;
use App\Services\Topic\TopicContentService;
use App\Services\Topic\TopicContentUserShieldService;
use App\Services\Topic\TopicService;
use App\Services\Topic\TopicUserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * ??????
     */
    public function register()
    {
        $this->registerMerUser();
        $this->registerGame();
        $this->registerSystem();
        $this->registerTopic();
        $this->registerChannel();
        $this->registerPay();
    }

    /**
     * MerUser
     */
    public function registerMerUser()
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

        $this->app->bind(MerUserFollowService::class, function () {
            return new MerUserFollowService(new MerUserFollow());
        });

        $this->app->bind(MerRetainLogService::class, function () {
            return new MerRetainLogService(new MerRetainLog());
        });

        $this->app->bind(MerUserCoinsLogService::class, function () {
            return new MerUserCoinsLogService(new MerUserCoinsLog());
        });
    }

    /**
     * Game
     */
    public function registerGame()
    {
        $this->app->bind(GameTagService::class, function () {
            return new GameTagService(new GameTag());
        });

        $this->app->bind(GamePackageService::class, function () {
            return new GamePackageService(new GamePackage());
        });

        $this->app->bind(AdBuryingService::class, function () {
            return new AdBuryingService(new AdBurying());
        });

        $this->app->bind(GamePackageSubscribeService::class, function () {
            return new GamePackageSubscribeService(new GamePackageSubscribe());
        });
    }

    /**
     * Topic
     */
    public function registerTopic()
    {
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

        $this->app->bind(TopicContentReportService::class, function () {
            return new TopicContentReportService(new TopicContentReport());
        });

        $this->app->bind(NoticeService::class, function () {
            return new NoticeService(new Notice());
        });

        $this->app->bind(TopicContentUserShieldService::class, function () {
            return new TopicContentUserShieldService(new TopicContentUserShield());
        });

        $this->app->bind(TopicContentResourceService::class, function () {
            return new TopicContentResourceService(new TopicContentResource());
        });

    }

    /**
     * System
     */
    public function registerSystem()
    {
        $this->app->bind(SysConfigService::class, function () {
            return new SysConfigService(new SysConfig());
        });

        $this->app->bind(SysFeedBackService::class, function () {
            return new SysFeedBackService(new SysFeedBack());
        });
    }

    /**
     * Channel
     */
    public function registerChannel()
    {
        $this->app->bind(ChannelGoogleService::class, function () {
            return new ChannelGoogleService(new ChannelGoogle());
        });
    }

    /**
     * Pay
     */
    public function registerPay()
    {
        $this->app->bind(PayProjectService::class, function () {
            return new PayProjectService(new PayProject());
        });
    }
}
