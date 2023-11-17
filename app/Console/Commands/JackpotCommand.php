<?php

namespace App\Console\Commands;


use App\Models\JackpotPool;
use App\Models\LuckyMoney;
use App\Models\TgUser;
use App\Services\ConfigService;
use App\Services\LuckyMoneyService;
use App\Services\TgUserService;
use App\Telegram\Middleware\GroupVerify;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Polling;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use function Termwind\breakLine;

class JackpotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jackpot';

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
        while (true) {
            $list = JackpotPool::query()->get();
            foreach ($list as $val){
                $jackpotCommission = ConfigService::getConfigValue($val['group_id'], 'jackpot');
                if($jackpotCommission>0){
                    $text = trans('telegram.jackpot_cumulative',['amount'=>$val['balance']]);
                    $bot->sendMessage($text, ['chat_id'=>$val['group_id'],'parse_mode' => ParseMode::HTML]);
                    sleep(3);
                }

            }
            sleep(60*5);
        }


    }


}
