<?php

namespace App\Console\Commands;


use App\Jobs\MsgToTelegram;
use App\Models\LuckyMoney;
use App\Models\TgUser;
use App\Services\ConfigService;
use App\Services\LuckyMoneyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

class ValidCheckCommand extends Command
{
    /** author [@cody](https://t.me/cody0512)
     * 红包过期判断
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validcheck';

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
        $i = 0;
        while (true) {

            $list = LuckyMoneyService::getInValidList();
            if (count($list) > 0) {
                foreach ($list as $item) {
                    $list = LuckyMoneyService::getLuckyHistory($item['id']);
                    $details = '';
                    $loseMoneyTotal = 0;
                    $profitTotal = 0;
                    $qiangTotal = 0;
                    foreach ($list as $key => $val) {
                        $qiangTotal += $val['amount'];
                        if ($val['is_thunder'] != 1) {
                            $details .= ($key + 1) . ".[💵] <code>" . number_format(round($val['amount'], 2), 2, '.', '') . "</code> U <code>" . format_name($val['first_name']) . "</code>\n";
                        } else {
                            $details .= ($key + 1) . ".[💣] <code>" . number_format(round($val['amount'], 2), 2, '.', '') . "</code> U <code>" . format_name($val['first_name']) . "</code>\n";

                            $loseMoney = $val['lose_money'];
                            $loseMoneyTotal += $loseMoney;
                            //平台抽成
                            $platformCommission = ConfigService::getConfigValue($val['chat_id'], 'platform_commission');
                            $platformCommissionAmount = 0;
                            if ($platformCommission > 0) {
                                $platformCommissionAmount = $loseMoney * $platformCommission / 100;
                            }
                            //jackpot抽成
                            $jackpotCommission = ConfigService::getConfigValue($val['chat_id'], 'jackpot');
                            $jackpotAmount = 0;
                            if ($jackpotCommission > 0) {
                                $jackpotAmount = $loseMoney * $jackpotCommission / 100;
                            }
                            $senderOwn = round($loseMoney - $platformCommissionAmount - $jackpotAmount, 2);

                            //上级抽成
                            $shareRate = ConfigService::getConfigValue($val['chat_id'], 'share_rate');
                            $shareUserId = TgUser::query()->where('tg_id', $val['sender_id'])->where('group_id', $val['chat_id'])->value('invite_user');
                            $shareRateAmount = 0;
                            if($shareUserId && $shareUserId != $val['sender_id']){
                                $shareRateAmount = $loseMoney * $shareRate / 100;
                                $senderOwn = round($senderOwn - $shareRateAmount, 2);
                            }
                            $profitTotal += $senderOwn;


                        }
                    }
                    $shengyu = round($item['amount'] - $qiangTotal, 2);
                    $shengyuText = $shengyu > 0 ? trans('telegram.valid_returned') : '';
                    $profit = round($profitTotal + $shengyu - $item['amount'], 2);
                    $profitTxt = $profit >= 0 ? '+' . $profit : $profit;
                    if ($item['type'] == 1) {

                        $caption = trans('telegram.valid_caption',[
                            'sender_name'=>format_name($item['sender_name']),
                            'luckyAmount'=>(int)$item['amount'],
                            'qiangTotal'=>round($qiangTotal, 2),
                            'lose_rate'=>round($item['lose_rate'], 2),
                            'thunder'=>$item['thunder'],
                            'details'=>$details,
                            'loseMoneyTotal'=>$loseMoneyTotal,
                            'profitTxt'=>$profitTxt,
                            'shengyuText'=>round($shengyu, 2) . $shengyuText,
                        ]);

                    } else {

                        $caption = trans('telegram.valid_caption',[
                            'sender_name'=>format_name($item['sender_name']),
                            'luckyAmount'=>(int)$item['amount'],
                            'qiangTotal'=>round($qiangTotal, 2),
                            'details'=>$details,
                            'shengyuText'=>round($shengyu, 2) . $shengyuText,
                        ]);
                    }

                    $data = [
                        'message_id' => $item['message_id'],
                        'chat_id' => $item['chat_id'],
                        'caption' => $caption,
                        'parse_mode' => ParseMode::HTML,
                        'reply_markup' => common_reply_markup($item['chat_id'])
                    ];
                    $num = 3;
                    for ($i = $num; $i >= 0; $i--) {
                        if ($i <= 0) {
                            Log::error('重试3次，过期信息编辑失败');
                            break;
                        }
                        try {
                            $rs = $bot->editMessageCaption($data);
                            $this->info("过期红包信息：message_id={$item['message_id']}  chat_id={$item['chat_id']}" . json_encode($item));
                            $this->doUpdate($item, $shengyu);
                            if (!$rs) {
                                Log::error('过期红包，信息编辑失败');
                            }
                            break;
                        } catch (\Exception $e) {
                            if ($e->getCode() == 429) {
                                $retry_after = $e->getParameter('retry_after');
                                sleep($retry_after);
                            } else {
                                $this->info("过期信息编辑失败，直接更新数据，返还金额。错误信息：" . $e);
                                Log::error('过期信息编辑失败，直接更新数据库。错误信息：' . $e);
                                $this->doUpdate($item, $shengyu);
                                break;
                            }
                        }
                    }
                }
            }
            sleep(30);
            $i++;
            $this->info("循环{$i}次");
        }
    }

    private function doUpdate($item,$shengyu){
        $uRs = LuckyMoney::query()->where('id', $item['id'])->where('status',1)->update(['status' => 3, 'updated_at' => date('Y-m-d H:i:s')]);
        if ($uRs) {
            //删除缓存
            del_lucklist($item['id']);
//            del_history($item['id']);
            $rs1 = TgUser::query()->where('tg_id', $item['sender_id'])->where('group_id', $item['chat_id'])->increment('balance', $shengyu);
            if (!$rs1) {
                LuckyMoney::query()->where('id', $item['id'])->update(['status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
            }
            money_log($item['chat_id'],$item['sender_id'],$shengyu,'bagback','红包过期返回',$item['id']);
        }
    }

}
