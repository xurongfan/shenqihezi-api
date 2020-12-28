<?php

namespace App\Http\Controllers\MerUser;

use App\Base\Controllers\Controller;
use App\Services\MerUser\MerUserFollowService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MerUserFollowController extends Controller
{
    protected  $service;

    /**
     * @param MerUserFollowService $service
     */
    public function __construct(MerUserFollowService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function follow(Request $request)
    {
        $this->validate($request,[
            'follow_user_id' => [
                'required' ,
                Rule::exists('mer_users','id')
            ],
        ],[
            'follow_user_id.required' => transL('mer-user.follow_user_id_empty_error'),
        ]);
        return $this->service->follow($request->follow_user_id);
    }

    /**
     * 我的粉丝
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        return $this->service->index();
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function myFollow()
    {
        return $this->service->myFollow();
    }
}
