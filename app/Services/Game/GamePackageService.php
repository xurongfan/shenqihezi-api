<?php

namespace App\Services\Game;

use App\Base\Services\BaseService;
use App\Models\Game\GamePackageSubscribe;
use App\Models\Game\GameTag;
use App\Services\MerUser\MerUserGameCollectionService;
use App\Services\MerUser\MerUserGameLikeService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class GamePackageService extends BaseService
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function index()
    {
        $result = $this->model->query()->selectRaw(DB::raw('
        game_package.id,
        game_package.title, 
        game_package.icon_img, 
        game_package.background_img, 
        game_package.url, 
        game_package.is_crack, 
        game_package.crack_url, 
        game_package.crack_des, 
        game_package.is_landscape, 
        game_package.is_rank, 
        game_package.like_base, 
        ( rand( ) * TIMESTAMP ( now( ) ) ) AS rid '))
//            ->leftJoin('game_package_tag','game_package_tag.package_id','=','game_package.id')
//            ->whereIn('game_package_tag.tag_id',$this->gameTagRandom())
//            ->groupBy('game_package.id')
            ->where('game_package.status',1)
            ->orderBy('game_package.is_rec','desc')
            ->orderBy(DB::raw('rid'),'desc')
            ->get();
       if ($result) {
           $gamePackageIds = Arr::pluck($result,'id');
            //是否喜欢
           $likeArr = app(MerUserGameLikeService::class)->findBy([
               'mer_user_id' => $this->userId(),
               'game_package_id' => [['in',$gamePackageIds]],
           ],'id,game_package_id');
           $likeArr = Arr::pluck($likeArr,'id','game_package_id');
            //是否收藏
           $collectArr = app(MerUserGameCollectionService::class)->findBy([
               'mer_user_id' => $this->userId(),
               'game_package_id' => [['in',$gamePackageIds]],
           ],'id,game_package_id');
           $collectArr = Arr::pluck($collectArr,'id','game_package_id');

           $likeCount = app(MerUserGameLikeService::class)->getModel()
               ->selectRaw('count(*) as count,game_package_id')
               ->whereIn('game_package_id',$gamePackageIds)
               ->groupBy('game_package_id')
               ->pluck('count','game_package_id')
               ->toArray();

           //是否订阅
           $subscribeArr = GamePackageSubscribe::query()
               ->where('mer_user_id',$this->userId())
               ->whereIn('game_package_id',$gamePackageIds)
               ->where('end_at','>',date('Y-m-d H:i:s'))
               ->pluck('id','game_package_id')
               ->toArray();
       }
       foreach ($result as $key => &$item) {
           $item['icon_img'] = ossDomain($item['icon_img']);
           $item['background_img'] = ossDomain($item['background_img']);
           $item['url'] = gameUrl($item['url']);

           $item['crack_url'] = gameUrl($item['crack_url'],$item['is_crack']);
           $item['is_like'] = isset($likeArr[$item['id']]) ? true : false;
           $item['is_collect'] = isset($collectArr[$item['id']]) ? true : false;
           $item['is_subscribe'] = isset($subscribeArr[$item['id']]) ? true : false;
           $item['like_count'] = isset($likeCount[$item['id']]) ? ($likeCount[$item['id']]+$item['like_base']) : 0;
       }
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