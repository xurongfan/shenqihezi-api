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
        try {
            $result = Storage::disk('oss')->put($folder.'/'.date('Ymd'), $file);
            return ossDomain($result);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        return $path;
    }
}
