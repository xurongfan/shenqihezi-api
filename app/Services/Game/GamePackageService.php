<?php

namespace App\Services\Game;

use App\Base\Services\BaseService;
use App\Models\Game\GamePackageTag;
use App\Models\Game\GameTag;
use Illuminate\Support\Facades\DB;

class GamePackageService extends BaseService
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function index()
    {
       $result = GamePackageTag::query()->selectRaw(DB::raw('
        DISTINCT game_package_tag.package_id as id,
        game_package.title, 
        game_package.icon_img, 
        game_package.background_img, 
        game_package.url, 
        game_package.is_crack, 
        game_package.crack_url, 
        game_package.crack_des, 
        game_package.is_landscape, 
        game_package.is_rank, 
        ( rand( ) * TIMESTAMP ( now( ) ) ) AS rid '))
            ->leftJoin('game_package','game_package_tag.package_id','=','game_package.id')
            ->whereIn('game_package_tag.tag_id',$this->gameTagRandom())
            ->orderBy(DB::raw('rid'),'desc')->limit(20)->get();
        return $result;
    }

    /**
     * 用户当前标签
     * @return array
     */
    public function tag()
    {
        $tagId = $this->user()->tagsId->toArray();
        return $tagId ? array_column($tagId,'tag_id') : [];
    }

    /**
     * 获取随机标签
     * @return array|\Illuminate\Database\Concerns\BuildsQueries[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function gameTagRandom()
    {
        $random =  GameTag::query()->selectRaw('id,(rand()*timestamp(now())) as rid')
            ->when($tags = $this->tag(),function ($query) use ($tags){
                $query->whereNotIn('id',$tags);
            })
            ->orderBy(DB::raw('rid'),'asc')->limit(count($tags) ? count($tags)-1 : 5)
            ->pluck('id')->toArray();

        return array_merge($tags,$random);
    }


}