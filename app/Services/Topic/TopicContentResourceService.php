<?php

namespace App\Services\Topic;

use App\Base\Services\BaseService;
use App\Jobs\TopicContentResourceJob;
use App\Models\Topic\TopicContent;
use Illuminate\Support\Carbon;

class TopicContentResourceService extends BaseService
{
    /**
     * @param $contentId
     * @param $resourceArr
     */
    public function resource($contentId,$resourceArr)
    {
        foreach ($resourceArr as $key => $item){
            $arr[] = [
                'mer_user_id' => $this->userId(),
                'content_id' => $contentId,
                'resource_url' => $item,
                'index' => $key,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ];
        }
        if (isset($arr) && $arr) {
            $this->model->query()->insert($arr);
            TopicContentResourceJob::dispatch($contentId);
        }
        return ;
    }

    /**
     * 异步或定期处理违规图片
     * @param int $contentId
     * @throws \AlibabaCloud\Client\Exception\ClientException
     */
    public function resourceCheck($contentId = 0)
    {
        $iClientProfile = \AlibabaCloud\Client\Profile\DefaultProfile::getProfile("cn-shanghai",env('ALI_ACCESS_KEYID'), env('ALI_ACCESS_SECRET')); // TODO
        $client = new \AlibabaCloud\Client\DefaultAcsClient($iClientProfile);

        $request = new \AlibabaCloud\Green\V20180509\ImageSyncScan();

        $request->setMethod("POST");
        $request->setAcceptFormat("JSON");
        $content = new TopicContent();
        $this->model->query()->when($contentId,function ($query) use ($contentId){
            $query->where('content_id',$contentId);
        })->where('status',0)
            ->chunkById(100,function ($resource) use ($request,$client,$content){
                foreach ($resource as $item){
                    try {
                        if (config('filesystems.green_image_scan')){
                            $task1 = array('dataId' =>  uniqid(),
                                'url' => $item['resource_url'],
                                'time' => round(microtime(true)*1000)
                            );
                            $request->setContent(json_encode(array("tasks" => array($task1),
                                "scenes" => array("porn"))));

                            $response = $client->getAcsResponse($request);

                            if(200 == $response->code){
                                $GreenData = json_decode(json_encode($response->data),true);
                                $resultData = $GreenData[0]['results'] ?? [];
                                $resultData = array_column($resultData,'suggestion');
                                $isGreen = in_array('block',$resultData) ? 2 : 1;
                                if ($isGreen == 2) {
                                    //违规图片替换
                                    $contentData = $content->query()->select('image_resource')->where('id',$item['content_id'])->first();
                                    $contentData = $contentData ? $contentData->toArray() : [];
                                    if ($contentData['image_resource'][$item['index']]) {
                                        $contentData['image_resource'][$item['index']] = 'https://resource.funtouchpal.com/unlawfulness.jpg?x-oss-process=style/yasuo';
                                        $content->query()->where('id',$item['content_id'])->update([
                                            'image_resource' => json_encode($contentData['image_resource'])
                                        ]);
                                    }
                                }

                                $item->update(['status'=>$isGreen]);
                            }
                        }
                    }catch (\Exception $exception){
                        logger('green image check:'.$item['resource_url'].$exception->getMessage());
                    }

                }
                usleep(1000);
        });
    }
}