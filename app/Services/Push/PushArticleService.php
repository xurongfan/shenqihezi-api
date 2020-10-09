<?php

namespace App\Services\Push;

use App\Base\Services\BaseService;
use App\Model\Channel\ChannelGroup;
use App\Model\Video\FirstVideo;
use App\Model\Video\SmallVideo;

class PushArticleService extends BaseService
{
    /**
     * @param $data
     * @return \App\Base\Services\Collection
     */
    public function list($data)
    {
        if (isset($data['title']) && $data['title']) {
            $data['title'] = [['like','%'.$data['title'].'%']];
        }
        $data = $this->model->buildQuery($this->model->filter($data))->orderBy($this->model->getTable().'.id', 'desc')->paginate($this->getPageSize($data))->toArray();
        foreach ($data['data'] as $k => &$item) {
            $item['type'] = isset($this->model->type_arr[$item['type']]) ? $this->model->type_arr[$item['type']] : '-';
        }
        return $data;
    }

    /**
     * @param $request
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function store($request)
    {
        $file = $request->file('file');
        if ($file){
            $data = self::importExcel($file);
        }

        // 应用切换
        $appPackageArr =array_filter(explode(',', $request->app_package));
        if (empty($appPackageArr)) {
            throw new \Exception('应用不能为空');
        }
        //视频 获取封面
        if ($request->type == $this->model::TYPE_VIDEO && $request->type_value){
            $videoInfo = FirstVideo::query()->selectRaw('id,title,bsy_img_url')->where('id', $request->type_value)->first();
            $request->video_title = $request->video_title ? $request->video_title : $videoInfo['title'];
            $thumbnailUrl = $videoInfo['bsy_img_url'] ?? '';
        }
        //视频 获取封面
        if ($request->type == $this->model::TYPE_SMALL_VIDEO && $request->type_value){
            $videoInfo = SmallVideo::query()->selectRaw('id,title,bsy_img_url')->where('id', $request->type_value)->first();
            $request->video_title = $request->video_title ? $request->video_title : $videoInfo['title'];
            $thumbnailUrl = $videoInfo['bsy_img_url'] ?? '';
        }
        $insertData = [
            'title'         => $request->title,
            'note_content'  => $request->note_content,
            'push_time'     => $request->push_type == 1 ? $request->push_time : date("Y-m-d H:i:s"),
            'type'          => $request->type,
            'push_type'     => $request->push_type,
            'type_value'    => $request->type_value,
            'video_title'   => $request->video_title,
            'thumbnail_url' => $thumbnailUrl??'',
            'user_type'     => $request->user_type,
            'pass_through'     => $request->pass_through,
            'device_tokens' => isset($data['device_tokens']) && $data['device_tokens'] ? implode(",", $data['device_tokens']) : '',
            'huawei_tokens' => isset($data['data_huawei']) && $data['data_huawei'] ? implode(",", $data['data_huawei']) : '',
            'vivo_tokens'   => isset($data['data_vivo']) && $data['data_vivo'] ? implode(",", $data['data_vivo']) : '',
            'oppo_tokens'   => isset($data['data_oppo']) && $data['data_oppo'] ? implode(",", $data['data_oppo']) : '',
            'xiaomi_tokens' => isset($data['data_xiaomi']) && $data['data_xiaomi'] ? implode(",", $data['data_xiaomi']) : '',
            'expire_time'   => $request->expire_time,
        ];
        if(isset($data['user_ids'])){
            $insertData['user_ids'] =  isset($data['user_ids']) && $data['user_ids'] ? implode(",", $data['user_ids']) : '';
        }
        $insertData['user_ids'] = $request->user_type == 10 ? '' : ($insertData['user_ids']??'');
        foreach ($appPackageArr as $app_package){
            $insertData['app_package'] = $app_package;
            $model = $this->save($insertData);
            $this->SendMissstion($request->push_type,$model->id);
        }
        return ;
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function view($id)
    {
        $info = $this->model->newQuery()->findOrFail($id);
        return $info;
    }
    /**
     * @param $id
     * @param array $request
     * @return mixed|void
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function edit($id, $request)
    {
        $file = $request->file('file');
        if ($file){
            $data = self::importExcel($file);
        }

        // 应用切换
        $appPackageArr = array_filter(explode(',', $request->app_package));//$request->app_package;//array_filter(explode(',', $request->app_package));
        if (empty($appPackageArr)) {
            throw new \Exception('应用不能为空');
        }
        //视频 获取封面
        if ($request->type == $this->model::TYPE_VIDEO && $request->type_value){
            $videoInfo = FirstVideo::query()->selectRaw('id,title,bsy_img_url')->where('id', $request->type_value)->first();
            $request->video_title = $request->video_title ? $request->video_title : $videoInfo['title'];
            $thumbnailUrl = $videoInfo['bsy_img_url'] ?? '';
        }
        //视频 获取封面
        if ($request->type == $this->model::TYPE_SMALL_VIDEO && $request->type_value){
            $videoInfo = SmallVideo::query()->selectRaw('id,title,bsy_img_url')->where('id', $request->type_value)->first();
            $request->video_title = $request->video_title ? $request->video_title : $videoInfo['title'];
            $thumbnailUrl = $videoInfo['bsy_img_url'] ?? '';
        }
        $insertData = [
            'title'         => $request->title,
            'note_content'  => $request->note_content,
            'push_time'     => $request->push_type == 1 ? $request->push_time : date("Y-m-d H:i:s"),
            'type'          => $request->type,
            'push_type'     => $request->push_type,
            'type_value'    => $request->type_value,
            'video_title'   => $request->video_title,
            'thumbnail_url' => $thumbnailUrl ?? '',
            'user_type'     => $request->user_type,
            'pass_through'     => $request->pass_through,
            'device_tokens' => isset($data['device_tokens']) && $data['device_tokens'] ? implode(",", $data['device_tokens']) : '',
            'huawei_tokens' => isset($data['data_huawei']) && $data['data_huawei'] ? implode(",", $data['data_huawei']) : '',
            'vivo_tokens'   => isset($data['data_vivo']) && $data['data_vivo'] ? implode(",", $data['data_vivo']) : '',
            'oppo_tokens'   => isset($data['data_oppo']) && $data['data_oppo'] ? implode(",", $data['data_oppo']) : '',
            'xiaomi_tokens' => isset($data['data_xiaomi']) && $data['data_xiaomi'] ? implode(",", $data['data_xiaomi']) : '',
            'expire_time'   => $request->expire_time,
        ];
        if(isset($data['user_ids'])){
            $insertData['user_ids'] =  isset($data['user_ids']) && $data['user_ids'] ? implode(",", $data['user_ids']) : '';
        }
        $insertData['user_ids'] = $request->user_type == 10 ? '' : ($insertData['user_ids']??'');
        $insertData['app_package'] = $appPackageArr[0];
        $this->updateBy(['id'=>$id],$insertData);
        $this->SendMissstion($request->push_type,$id);
        return $id;
    }

    /**
     * @param $state
     * @param $id
     * @return array
     */
    public function SendMissstion($state,$id){
        if($state==1){
            //定时发送--todo
            $url = env('SPRING_TASK_SATAT', 'http://dev.npush-java.uheixia.com/api/springTask/initPushTask');
            $result =postCurl($url,'','GET');
            return ['java_url'=>$url,'push_result'=>$result];
        }elseif ($state==2){
            $data = [
                'id'=>$id
            ];
            //立即发送--todo
            $pushurl = env('JAVA_API_URL', 'http://dev.npush-java.uheixia.com')."/api/uPush/realTimeSendInfo";
            $res2 = postCurl($pushurl,$data,'POST');

//            $url1 = env('JAVA_REC_FIRST_VIDEO_ACTION_DOMAIN', 'http://dev.rec.uheixia.com') . "/api/uPush/realTimeSendInfo";
//            postCurl($url1, $data, $type = "POST");

            return ['java_url'=>$pushurl,'push_result'=>$res2];
        }
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function destroy($id)
    {
        if ($id) {
            $id = is_array($id) ? $id : explode(',',$id) ;
            $this->model->newQuery()->when(is_array($id),function ($query) use ($id){
                return $query->whereIn('id',$id);
            }, function ($query) use ($id){
               return $query->where('id', $id);
            })->delete();
            return;
        }
        throw new \Exception('数据有误');
    }

    /**
     * @param $excelFile
     * @return array
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    private function importExcel($excelFile)
    {
        $device_tokens=[];
        $data_huawei=[];
        $data_vivo=[];
        $data_oppo=[];
        $data_xiaomi=[];
        $PHPExcel = \PHPExcel_IOFactory::load($excelFile);
        $currentSheet = $PHPExcel->getSheet(0);
        $allColumn = $currentSheet->getHighestColumn();
        $allRow = $currentSheet->getHighestRow();

        for ($currentRow = 3; $currentRow <= $allRow; $currentRow++) {
            //从哪列开始，A表示第一列
            //数据坐标
            $address0 = "A" . $currentRow;
            $address = "D" . $currentRow;
            $address1 = "E" . $currentRow;
            $address_vivo = "F" . $currentRow;
            $address1_oppo = "G" . $currentRow;
            $address1_xiaomi = "H" . $currentRow;
            //读取到的数据，保存到数组$arr中
            if(iconv("GB2312","UTF-8//IGNORE",$currentSheet->getCell($address0)->getValue())!=""){
                $user_ids[] =iconv("GB2312","UTF-8//IGNORE",$currentSheet->getCell($address0)->getValue());
            }
            if(iconv("GB2312","UTF-8//IGNORE",$currentSheet->getCell($address)->getValue())!=""){
                $data_huawei[] =iconv("GB2312","UTF-8//IGNORE",$currentSheet->getCell($address)->getValue());
            }
            if(iconv("GB2312","UTF-8//IGNORE",$currentSheet->getCell($address1)->getValue())!=""){
                $device_tokens[] =iconv("GB2312","UTF-8//IGNORE",$currentSheet->getCell($address1)->getValue());
            }

            if(str_replace("*","",iconv("GB2312","UTF-8//IGNORE",$currentSheet->getCell($address_vivo)->getValue()))!=""){
                $data_vivo[] =str_replace("*","",iconv("GB2312","UTF-8//IGNORE",$currentSheet->getCell($address_vivo)->getValue()));
            }
            if(str_replace("*","",iconv("GB2312","UTF-8//IGNORE",$currentSheet->getCell($address1_oppo)->getValue()))!=""){
                $data_oppo[] =str_replace("*","",iconv("GB2312","UTF-8//IGNORE",$currentSheet->getCell($address1_oppo)->getValue()));
            }
            if(str_replace("*","",iconv("GB2312","UTF-8//IGNORE",$currentSheet->getCell($address1_xiaomi)->getValue()))!=""){
                $data_xiaomi[] =str_replace("*","",iconv("GB2312","UTF-8//IGNORE",$currentSheet->getCell($address1_xiaomi)->getValue()));
            }
        }
        if(!$device_tokens && !$data_huawei && !$data_vivo && !$data_oppo && !$data_xiaomi){
            throw new \Exception('文件内设备号为空');
        }
        return compact('device_tokens','data_huawei','data_vivo','data_oppo','data_xiaomi','user_ids');
    }

    /**
     * @return array
     */
    public function config()
    {
        $channel = ChannelGroup::query()->where('s_id', 2)->pluck('name','url');
        $type_arr = $this->model->type_arr;
        $push_channel_arr = $this->model->push_channel_arr;
        $appUrl = $this->model->appUrl;
        return compact('channel','type_arr','push_channel_arr','appUrl');
    }
}