<?php

namespace App\Telegram\Middleware;

use App\Models\AuthGroup;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;

class GroupVerify
{

    public function __invoke(Nutgram $bot, $next): void
    {
//        Log::info('消息日志4：'.json_encode($bot->update()));
        if($bot->chat()->type == 'private'){
        }else{

            $count = AuthGroup::query()->where('status', 1)->where('group_id', $bot->chat()->id)->count();
            if ($count > 0) {
                $next($bot);
            } else {
                try{
                    $bot->sendMessage('未授权');
                }catch (\Exception $e){
                    Log::error('GroupVerify错误'.$e);
                }

            }
        }



    }
}
