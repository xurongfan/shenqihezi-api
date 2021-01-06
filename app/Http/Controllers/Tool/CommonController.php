<?php

namespace App\Http\Controllers\Tool;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CommonController extends Controller
{
    /**
     * @return string
     * @throws \Exception
     */
    public function upload()
    {
        $file = request()->file('file');
        $folder = request()->file('folder','content');//profile_imgs
        $folder = env('APP_NAME') ? env('APP_NAME','').'-'.$folder : $folder;
        try {
            $result = Storage::disk('oss')->put($folder.'/'.date('Ymd'), $file);
            if (strpos($folder,'profile_imgs') !== false && config('filesystems.green_image_scan')){
                $iClientProfile = \AlibabaCloud\Client\Profile\DefaultProfile::getProfile("cn-shanghai",env('ALI_ACCESS_KEYID'), env('ALI_ACCESS_SECRET')); // TODO
                $client = new \AlibabaCloud\Client\DefaultAcsClient($iClientProfile);

                $request = new \AlibabaCloud\Green\V20180509\ImageSyncScan();

                $request->setMethod("POST");
                $request->setAcceptFormat("JSON");

                $task1 = array('dataId' =>  uniqid(),
                    'url' => 'http://'.config('filesystems.disks.oss.bucket').".".
                        config('filesystems.disks.oss.endpoint')."/".
                        $result,
                    'time' => round(microtime(true)*1000)
                );
                $request->setContent(json_encode(array("tasks" => array($task1),
                    "scenes" => array("porn"))));

                $response = $client->getAcsResponse($request);

                if(200 == $response->code){
                    $GreenData = json_decode(json_encode($response->data),true);
                    $resultData = $GreenData[0]['results'] ?? [];
                    $resultData = array_column($resultData,'suggestion');
                   if (in_array('block',$resultData)){
                       throw new \Exception(transL('common.green_image_block'));
                   }
                }

            }

            return ossDomain($result);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }



        return $path;
    }
}
