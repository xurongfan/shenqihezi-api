<?php

namespace App\Http\Controllers\Tool;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommonController extends Controller
{
    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function baiShanCloud(Request $request) {

        $this->validate($request, [
            'file' => 'required|image',
        ]);

        $project = env('BAISHANCLOUD_PROJECT', 'dev-img-xiyou');

        $result = baiShanCloudUpload($request->file('file'), $project);


        if ($result != -1) {
            if (strstr($result, 'svga')) {
                $type = 2;
            }
            return ['file' => $result, 'type' => $type ?? 1];
        }
       throw new \Exception('上传失败');
    }
}
