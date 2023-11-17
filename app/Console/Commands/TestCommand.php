<?php

namespace App\Console\Commands;


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

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tgtest';

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
        echo trans('telegram.leopard_reward',['userName'=>1111,'redAmount'=>1222,'leopardReward'=>'12.212']);
        //$this->testred();
    }

    public function testred(){
        for ($a = 5000; $a <= 6100; $a++) {
            $this->info("aaaaaaaaaaaaaaaaaaa    {$a}    aaaaaaaaaaaaaaaaaa");
            sleep(1);
            $totalAmount = $a;
            $totalCount = 6;
            $minAmount = 0.1;
            $maxAmount = $totalAmount / $totalCount * 2; // 每个红包最大金额
            for ($t = 0; $t <= 9; $t++) {
                $this->info("tttttttttttttttttt   {$t}   ttttttttttttttttttttt");
                sleep(1);
                $thunder = $t;
                $chance = 50;
                for ($i = 0; $i < 10000; $i++) {
                    $redEnvelopes = red_envelope($totalAmount, $totalCount, $minAmount, $maxAmount, $thunder, 1,$chance);
                    $resultTotal = 0;
                    foreach ($redEnvelopes as $item) {
                        if ($item <= 0) {

                            echo '==a0a==';
                            pp($redEnvelopes);
                            break 2;
                        }
                        $resultTotal = bcadd($resultTotal, $item, 2);
//                $this->info('$resultTotal='.$resultTotal);
                    }
                    if(count(array_unique($redEnvelopes)) != $totalCount){
                        echo "不等于\n";
                        break;
                    }
                    $resultTotal = round($resultTotal, 2);
                    $this->info($resultTotal);
                    print_r($redEnvelopes);
//            sleep(1);

                    echo "****a={$a}**t={$t}******i={$i}**********\n";
                    if ($resultTotal != $totalAmount) {
                        var_dump($resultTotal);
                        var_dump($totalAmount);
                        echo 'poo';
                        break;
                    }
                }
            }
        }
    }

}
