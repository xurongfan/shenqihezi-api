<?php
/**
 * Created by PhpStorm.
 * User: 98du
 * Date: 2020/8/27
 * Time: 10:06
 */

namespace App\Http\Controllers\Push;


use App\Base\Controllers\Controller;
use App\Services\Push\PushArticleService;
use App\Services\Push\PushResultService;
use Illuminate\Http\Request;

class PushArticleController extends Controller
{
    protected $service;


    public function __construct(PushArticleService $service)
    {
        $this->service = $service;
    }

    /**
     * 列表
     * @return \App\Base\Services\Collection
     */
    public function list()
    {
        return $this->service->list(request()->all());
    }

    /**
     * 添加推送计划
     * @param Request $request
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    /**
     * 获取推送计划
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function view(Request $request)
    {
        $id = $request->get('id',0);
        return $this->service->view($id);
    }

    /**
     * 修改推送计划
     * @param Request $request
     * @return mixed|void
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function update(Request $request)
    {
        $id = $request->get('id',0);
        return $this->service->edit($id,$request);
    }

    /**
     * 删除推送计划
     * @throws \Exception
     */
    public function destroy()
    {
        $id = \request()->input('id');
        return $this->service->destroy($id);
    }

    /**
     * 推送报表记录
     * @return mixed
     */
    public function pushResult(Request $request)
    {
        return app(PushResultService::class)->index($request);
    }

    /**
     * 获取推送配置信息
     * @return array
     */
    public function pushConfig()
    {
        return $this->service->config();
    }

    /**
     * 推送数据定时入库
     * @return mixed
     */
    public function autoResult(Request $request)
    {
        return app(PushResultService::class)->auto($request->all());
    }

}