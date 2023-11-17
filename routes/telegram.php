<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Models\InviteLink;
use App\Services\ConfigService;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

/*
|--------------------------------------------------------------------------
| Nutgram Handlers
|--------------------------------------------------------------------------
|
| Here is where you can register telegram handlers for Nutgram. These
| handlers are loaded by the NutgramServiceProvider. Enjoy!
|
*/

//$bot->onCommand('start', function (Nutgram $bot) {
//    return $bot->sendMessage('Hello, world!');
//})->description('The start command!');
$bot->onText('('.trans('telegram.groupinfo').'$)', function (Nutgram $bot, $ac) {
    if ($bot->chat()->type == 'private') {
    }else{
        if($ac==trans('telegram.groupinfo')){
            $params = [
                'parse_mode' => ParseMode::HTML
            ];
            $bot->sendMessage(trans('telegram.group_id')."：<code>{$bot->chat()->id}</code>\n".trans('telegram.user_id')."：<code>{$bot->user()->id}</code>", $params);
        }

    }
});
$bot->onPhoto(function (Nutgram $bot) {
    if ($bot->chat()->type == 'private') {
        $fileId = $bot->message()->photo[1]->file_id;
        $params = [
            'parse_mode' => ParseMode::HTML
        ];

        $bot->sendMessage(trans('telegram.photo')." ID：<code>$fileId</code>", $params);
    }
});
$bot->onCommand('help(.*)', function (Nutgram $bot) {
    $helpText = ConfigService::getConfigValue($bot->chat()->id, 'help');
    if ($helpText) {
        $params = [
            'parse_mode' => ParseMode::HTML
        ];
        if (!empty($bot->message()->message_id)) {
            $params['reply_to_message_id'] = $bot->message()->message_id;
        }
        try{
            $bot->sendMessage($helpText,$params);
        } catch (\Exception $e) {
            $bot->sendMessage($helpText, ['parse_mode' => ParseMode::HTML]);
        }

    }
});
$bot->onCommand('invite(.*)', function (Nutgram $bot) {
    \App\Services\TelegramService::invite_link($bot);
});

$bot->onCommand('start', function (Nutgram $bot) {
    $text = trans('telegram.start_msg',['userId'=>$bot->user()->id]);
    $bot->sendMessage($text, ['parse_mode' => ParseMode::HTML]);
});
