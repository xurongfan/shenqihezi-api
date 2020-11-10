<?php

namespace App\Services\Game;

use App\Base\Services\BaseService;

class GamePackageService extends BaseService
{
    
    public function index()
    {
        $tagId = $this->user()->tagsId->toArray();
        if ($tagId) {
            $tagId = array_column($tagId,'tag_id');
        }
    }
}