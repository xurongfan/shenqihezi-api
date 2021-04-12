<?php

namespace App\Models\Game;


use App\Base\Models\BaseModel;

class GamePackage extends BaseModel
{
    protected $table = 'game_package';

    public static $modify = true;

    /**
     * 是否订阅
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function subscribe()
    {
        return $this->hasOne(GamePackageSubscribe::class,'game_package_id','id');
    }

    /**
     * @param $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        return config('app.locale') == env('APP_LOCALE','en') ? $value : transL('game-package.title'.'_'.$this->id);
    }

    /**
     * @param $value
     * @return string
     */
    public function getIconImgAttribute($value)
    {
        return ossDomain($value);
    }

    /**
     * @param $value
     * @return string
     */
    public function getBackgroundImgAttribute($value)
    {
        return ossDomain($value);
    }

    /**
     * @param $value
     * @return string
     */
    public function getUrlAttribute($value)
    {
        return !self::$modify || $this->status == 1 ? gameUrl($value) : '';
    }

    /**
     * @param $value
     * @return string
     */
    public function getCrackUrlAttribute($value)
    {
        return !self::$modify || $this->status == 1 ? gameUrl($value,1) : '';
    }

    /**
     * @param $value
     * @return string
     */
    public function getVideoUrlAttribute($value)
    {
        return ossDomain($value);
    }
}
