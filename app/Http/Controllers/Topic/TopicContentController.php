<?php

namespace App\Http\Controllers\Topic;

use App\Base\Controllers\Controller;
use App\Services\Topic\TopicContentCommentLikeService;
use App\Services\Topic\TopicContentCommentService;
use App\Services\Topic\TopicContentLikeService;
use App\Services\Topic\TopicContentReportService;
use App\Services\Topic\TopicContentService;
use App\Services\Topic\TopicContentUserShieldService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class TopicContentController
 * @package App\Http\Controllers\Topic
 */
class TopicContentController extends Controller
{

    /**
     * 发表话题内容
     * @param Request $request
     * @return mixed
     */
    public function publish(Request $request)
    {
        $this->validate($request,[
//            'topic' => 'required|array' ,
            'game_package_id' => [
                Rule::exists('game_package','id')
            ],
        ],[
//            'topic.required' => transL('topic.topic_empty_error'),
        ]);
        return app(TopicContentService::class)->publish(\request()->all());
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelAnonymous(Request $request)
    {
        $this->validate($request,[
            'content_id' => [
                'required' ,
                Rule::exists('topic_content','id')
            ],
        ],[
            'content_id.required' => transL('topic.content_id_empty_error'),
        ]);
        return app(TopicContentService::class)->cancelAnonymous($request->content_id);
    }

    /**
     * 发表评论
     * @param Request $request
     * @return mixed
     */
    public function comment(Request $request)
    {
        $this->validate($request,[
            'content_id' => [
                'required' ,
                Rule::exists('topic_content','id')
            ],
            'comment' => [
                'required' ,
            ]
        ],[
            'content_id.required' => transL('topic.content_id_empty_error'),
        ]);
        return app(TopicContentCommentService::class)->publish($request->all());
    }

    /**
     * 删除评论
     * @param Request $request
     * @return mixed
     */
    public function deleteComment(Request $request)
    {
        $this->validate($request,[
            'comment_id' => [
                'required' ,
                Rule::exists('topic_content_comment','id')
            ],
        ]);
        return app(TopicContentCommentService::class)->deleteComment($request->comment_id);
    }

    /**
     * 评论列表
     * @param Request $request
     * @return mixed
     */
    public function commentList(Request $request)
    {
        $this->validate($request,[
            'content_id' => [
                'required' ,
                Rule::exists('topic_content','id')
            ],
        ],[
            'content_id.required' => transL('topic.content_id_empty_error'),
        ]);
        return app(TopicContentCommentService::class)->index($request->content_id,$request->pid);
    }

    /**
     * 点赞评论
     * @param Request $request
     * @return mixed
     */
    public function commentLike(Request $request)
    {
        $this->validate($request,[
            'comment_id' => [
                'required' ,
                Rule::exists('topic_content_comment','id')
            ],
        ],[
            'comment_id.required' => transL('topic.comment_id_empty_error'),
        ]);
        return app(TopicContentCommentLikeService::class)->like($request->comment_id);
    }

    /**
     * 我的评论
     * @param Request $request
     * @return mixed
     */
    public function myComment(Request $request)
    {
        return app(TopicContentCommentService::class)->myComment();
    }

    /**
     * 话题内容列表
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
//        if ($request->is_follow){
//            $this->middleware = ['auth_token'];
//        }
        return app(TopicContentService::class)->index($request->is_follow,$request->topic_id,$request->is_hot,$request->mer_user_id);
    }

    /**
     * 话题详情
     * @param $contenId
     * @return mixed
     */
    public function show($contentId)
    {
        return app(TopicContentService::class)->show($contentId);
    }

    /**
     * 我的话题内容
     * @return mixed
     */
    public function myTopicContent()
    {
        return app(TopicContentService::class)->index(0,0,0,-1);
    }

    /**
     * 话题内容点赞
     * @param Request $request
     * @return mixed
     */
    public function like(Request $request)
    {
        $this->validate($request,[
            'content_id' => [
                'required' ,
                Rule::exists('topic_content','id')
            ],
        ],[
            'content_id.required' => transL('topic.content_id_empty_error'),
        ]);
        return app(TopicContentLikeService::class)->like($request->content_id);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function delete(Request $request)
    {
        $this->validate($request,[
            'content_id' => [
                'required' ,
                Rule::exists('topic_content','id')
            ],
        ],[
            'content_id.required' => transL('topic.content_id_empty_error'),
        ]);

        return app(TopicContentService::class)->deleteContent($request->content_id);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function report(Request $request)
    {
        $this->validate($request,[
            'topic_id' => [
                Rule::exists('topic','id')
            ],
            'content_id' => [
                Rule::exists('topic_content','id')
            ],
            'comment_id' => [
                Rule::exists('topic_content_comment','id')
            ],
            'report_content' => [
                'required'
            ]
        ]);

        return app(TopicContentReportService::class)->store($request->topic_id,$request->content_id,$request->comment_id,$request->report_content);
    }

    /**
     * 屏蔽用户
     * @return mixed
     */
    public function shield(Request $request)
    {
        $this->validate($request,[
            'shield_user_id' => [
                'required' ,
                Rule::exists('mer_users','id')
            ],
        ]);
        return app(TopicContentUserShieldService::class)->store($request->shield_user_id);
    }

    /**
     * @return mixed
     */
    public function gameTopic()
    {
        return app(TopicContentService::class)->gameTopic();
    }
}
