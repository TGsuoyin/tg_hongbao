<?php

namespace App\Console\Commands;


use App\Jobs\MsgToTelegram;
use App\Models\LuckyMoney;
use App\Models\TgUser;
use App\Services\ConfigService;
use App\Services\LuckyMoneyService;
use App\Services\TelegramService;
use App\Services\TgUserService;
use App\Telegram\Middleware\GroupVerify;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Polling;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class AutoGetCommand extends Command
{
    /**
     * author [@cody](https://t.me/cody0512)
     * 自动抢包
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autoget';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $telegram;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Nutgram $bot)
    {
        $this->info('开始...');
        while (true) {
            sleep(rand(1, 2));
            $luckyList = LuckyMoney::query()->where('status', 1)->get();
            if ($luckyList->isEmpty()) {
                continue;
            }
            $luckyList = $luckyList->toArray();

            foreach ($luckyList as $lucy) {
                sleep(rand(1, 3));
                $validTime = ConfigService::getConfigValue($lucy['chat_id'], 'valid_time');
                if ($lucy['created_at'] > date('Y-m-d H:i:s', strtotime("- {$validTime}seconds")) && $lucy['number'] > $lucy['received_num']) {
                    $passSec = ConfigService::getConfigValue($lucy['chat_id'], 'auto_get_sec');
                    if($passSec == 0){
                        continue;
                    }
                    if ($lucy['created_at'] < date('Y-m-d H:i:s', strtotime("- {$passSec} seconds"))) {
                        $botUsers = TgUser::query()->where('group_id',$lucy['chat_id'])->where('auto_get', 1)->get();
                        if ($botUsers->isEmpty()) {
                            continue;
                        }
                        $botUsers = $botUsers->toArray();
                        $userIds = array_column($botUsers, 'tg_id');
                        shuffle($userIds);
                        for ($j = 0; $j <= $lucy['number'] - $lucy['received_num']; $j++) {
                            if (isset($userIds[$j])) {
                                $userId = $userIds[$j];
//                                $rs = TelegramService::qiangAction($bot, $lucy['id'], $userId,$lucy['message_id']);
                                if(env('QUEUE_CONNECTION') == 'sync'){
                                    $rs = TelegramService::qiangAction($bot, $lucy['id'], $userId,$lucy['message_id']);
//                                if (!$rs) {
//                                    continue;
//                                }
                                }else{
                                    $jobData = [
                                        'lucky_id' => $lucy['id'],
                                        'chat_id' => $lucy['chat_id'],
                                        'user_id' => $userId,
                                        'message_id' => $lucy['message_id'],
                                        'callback_query_id' => null,
                                    ];
                                    MsgToTelegram::dispatch($jobData)->onQueue('qiang');
                                }

                                break;
                            } else {
                                sleep(1);
                                break;
                            }
                        }
                    }

                }
            }

        }

    }
}
