<?php


namespace App\Models\Game;


use App\Base\Models\BaseModel;

class GameType extends BaseModel
{
    protected $table = 'game_type';

    /**
     * @param $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        $lang = config('app.locale');
        $localLang = scandir(resource_path('lang'));
        unset($localLang[0],$localLang[1]);
        $lang = in_array($lang,$localLang)?$lang:env('APP_LOCALE','en');
        return $lang == env('APP_LOCALE','en') ? $value : transL('game-type.title'.'_'.$this->id,'',[],$lang);
    }
}