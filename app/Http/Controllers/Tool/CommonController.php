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
        try {
           return Storage::disk('oss')->put('profile_imgs/'.date('Ymd'), $file);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        return $path;
    }
}
