<?php

namespace App\Services;

use App\Models\Config;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class ConfigService
{

    public function __construct()
    {

    }

    public static function getConfigValue($groupId,$name,$cacheTime = 10){
        $value = Redis::get('config_'.$groupId.'_'.$name);
        if(!$value){
            $info =  Config::query()->where('group_id', $groupId)->where('name', $name)->first();
            if(!$info){
                if(config('tgbot.'.$name) >0){
                    return config('tgbot.'.$name);
                }
                return '';
            }else{
                $value = $info['value'];
                Redis::setex('config_'.$groupId.'_'.$name,$cacheTime,$value);
                return $value;
            }

        }else{
            return $value;
        }

    }
}
