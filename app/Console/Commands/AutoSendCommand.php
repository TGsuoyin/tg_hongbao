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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Polling;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class AutoSendCommand extends Command
{
    /**
     * author [@cody](https://t.me/cody0512)
     * 自动发包
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autosend';

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

            $amount = mt_rand(10,100);
            $mine = mt_rand(1,9);
            echo $amount.'-'.$mine."\n";
            $user = TgUser::query()->where('balance','>',100)->where('group_id',-1001937897351)->orderBy(DB::raw('rand()'))->first();

            if(!$user){
                Log::error('群里未发现用户，请先添加用户再操作');
                $this->info('群里未发现用户，请先添加用户再操作');
                break;
            }
            TelegramService::fabao($bot,'',$amount,$mine,-1001937897351,$user['tg_id']);

            sleep(mt_rand(60,100));
        }

    }
}
