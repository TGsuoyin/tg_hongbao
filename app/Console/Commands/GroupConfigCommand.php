<?php

namespace App\Console\Commands;


use App\Models\AuthGroup;
use App\Models\LuckyMoney;
use App\Models\TgUser;
use App\Services\ConfigService;
use App\Services\LuckyMoneyService;
use App\Services\TgUserService;
use App\Telegram\Middleware\GroupVerify;
use Dcat\Admin\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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

class GroupConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gconfig';

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
        $list = AuthGroup::query()->get();
        $tgbotConfig = config('tgbot');
        foreach ($list as $item){
            foreach ($tgbotConfig as $key => $val) {
                if (\App\Models\Config::query()->where('name', $key)->where('group_id', $item['group_id'])->count() == 0) {
                    $insert = [
                        'name' => $key,
                        'value' => $val,
                        'group_id' => $item['group_id'],
                        'admin_id' => 1,
                        'remark' => trans('admin.tgbot.' . $key),
                    ];
                    \App\Models\Config::query()->create($insert);
                }
            }
        }
        $this->info('设置成功');
    }


}
