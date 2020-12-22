<?php

namespace App\Http\Controllers\Topic;

use App\Services\Notice\NoticeService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NoticeController extends Controller
{
    protected $service;

    /**
     * NoticeController constructor.
     * @param NoticeService $service
     */
    public function __construct(NoticeService $service)
    {
        $this->service = $service;
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        return $this->service->index();
    }

    /**
     * @return int
     */
    public function noticeCount()
    {
        return $this->service->noticeCount();
    }
}
