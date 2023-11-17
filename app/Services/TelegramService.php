<?php

namespace App\Services;

use App\Jobs\LuckyHistoryJob;
use App\Jobs\MsgToTelegram;
use App\Models\AuthGroup;
use App\Models\InviteLink;
use App\Models\JackpotPool;
use App\Models\JackpotReward;
use App\Models\LuckyHistory;
use App\Models\LuckyMoney;
use App\Models\RechargeRecord;
use App\Models\TgUser;
use App\Models\WithdrawRecord;
use App\Telegram\Middleware\GroupVerify;
use Dcat\Admin\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Polling;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

/**
 * author [@cody](https://t.me/cody0512)
 */
class TelegramService
{

    public static function handleRed(Nutgram $bot)
    {

        $bot->group(GroupVerify::class, function (Nutgram $bot) {

            // Your handlers here
            // Called when a message contains the command "/start someParameter"
//            $bot->onCommand('start {parameter}', function (Nutgram $bot, $parameter) {
//                $bot->sendMessage("The parameter is {$parameter}");
//            });
            // ex. called when a message contains "My name is Mario"
            $bot->onText('('.trans('telegram.recharge').'|\+)([0-9]+)', function (Nutgram $bot,$ac, $amount) {
                self::shangfen($bot,$amount);
            });
            $bot->onText('('.trans('telegram.withdraw').'|-)([0-9]+)', function (Nutgram $bot,$ac,  $amount) {
                self::xiafen($bot,$amount);

            });
            $bot->onText('(发[包]*)*([0-9]+\.?[0-9]?)[-/]([0-9]+\.?[0-9]?)', function (Nutgram $bot, $ac, $amount, $mine) {
                self::fabao($bot, $ac, $amount, $mine);
            });
            $bot->onText(trans('telegram.welfare').'([0-9]+\.?[0-9]?)[-/]([0-9]+\.?[0-9]?)', function (Nutgram $bot, $amount, $num) {
                self::fuli($bot, $amount, $num);
            });

            $bot->onText('(1$|查$|余额$|balance$|ye$|query$)', function (Nutgram $bot, $ac) {
                if ($ac == '1' || $ac == 'ye'|| $ac == 'balance'|| $ac == 'query' || $ac == '查' || $ac == '余额' || $ac == '查余额') {
                    self::cha($bot);
                }
            });

            $bot->onCallbackQueryData('balance', function (Nutgram $bot) {
                $userInfo = TgUserService::getUserById($bot->user()->id, $bot->chat()->id);
                if (!$userInfo) {
                    $bot->answerCallbackQuery([
                        'text' => trans('telegram.notregistered'),
                        'show_alert' => true,
                        'cache_time' => 5
                    ]);

                } else {
                    $bot->answerCallbackQuery([
                        'text' => "{$userInfo->first_name} \n@{$userInfo->username} \n-----------------------------\nID：{$userInfo->tg_id}\n".trans('telegram.balance')."：{$userInfo->balance}  U",
                        'parse_mode' => ParseMode::HTML,
                        'show_alert' => true,
                        'cache_time' => 5
                    ]);
                }

            });
            $bot->onCallbackQueryData('invitelink', function (Nutgram $bot) {
                self::invite_link($bot);

            });
            $bot->onCallbackQueryData('qiang-{luckyid}', function (Nutgram $bot, $luckyid) {
                $userId = $bot->user()->id;
                if (env('QUEUE_CONNECTION') == 'sync') {
                    self::qiangAction($bot, $luckyid, $userId, $bot->message()->message_id, $bot->callbackQuery()?->id);
                } else {
                    $jobData = [
                        'chat_id' => $bot->chat()->id,
                        'lucky_id' => $luckyid,
                        'user_id' => $userId,
                        'message_id' => $bot->message()->message_id,
                        'callback_query_id' => $bot->callbackQuery()?->id,
                    ];
                    MsgToTelegram::dispatch($jobData)->onQueue('qiang');
                }
//                Log::info('qiang=>' . json_encode($bot->message(), JSON_UNESCAPED_UNICODE));
                //

            });

            $bot->onCallbackQueryData('today_data', function (Nutgram $bot) {
                $result = TgUserService::getTodayData($bot->user()->id, $bot->chat()->id);
                if($result['state']==0){
                    $bot->answerCallbackQuery([
                        'text' => $result['msg'],
                        'show_alert' => true,
                        'cache_time' => 10
                    ]);
                    return false;
                }
                $data = $result['data'];
                $text = trans('telegram.todayprofit')."：{$data['todayProfit']}
-----------
".trans('telegram.expenditure')."：-{$data['redPayTotal']}
".trans('telegram.awarding')."：+{$data['sendProfitTotal']}
-----------
".trans('telegram.bagincome')."：+{$data['getProfitTotal']}
".trans('telegram.thunderlose')."：-{$data['loseTotal']}
-----------
".trans('telegram.inviterebate')."：+{$data['todayInvite']}
".trans('telegram.shareprofit')."：+{$data['todayShare']}";
                /*
                $text.="
-----------
平台抽成：-{$result['todayPlat']}
上级代理抽成：-{$result['todayTopShare']}
Jackpot抽成：-{$result['todayJackpot']}";
                */
                $bot->answerCallbackQuery([
                    'text' => $text,
                    'show_alert' => true,
                    'cache_time' => 10
                ]);
            });

            $bot->onCallbackQueryData('team_report', function (Nutgram $bot) {
                $result = TgUserService::getTeamData($bot->user()->id, $bot->chat()->id);
                if($result['state']==0){
                    $bot->answerCallbackQuery([
                        'text' => $result['msg'],
                        'show_alert' => true,
                        'cache_time' => 10
                    ]);
                    return false;
                }
                $data = $result['data'];
                $text = trans('telegram.todayprofit')."：{$data['todayProfit']}
".trans('telegram.todayrecharge')."：{$data['todayRecharge']}
".trans('telegram.todaywithdraw')."：{$data['todayWithdraw']}
".trans('telegram.todaysendamount')."：{$data['todaySendAmount']}";

                $bot->answerCallbackQuery([
                    'text' => $text,
                    'show_alert' => true,
                    'cache_time' => 10
                ]);
            });
            $bot->onCallbackQueryData('yesterday_data', function (Nutgram $bot) {
                $result = TgUserService::getYesterdayData($bot->user()->id, $bot->chat()->id);
                if($result['state']==0){
                    $bot->answerCallbackQuery([
                        'text' => $result['msg'],
                        'show_alert' => true,
                        'cache_time' => 10
                    ]);
                    return false;
                }
                $data = $result['data'];
                $text = trans('telegram.yesterdayprofit')."：{$data['todayProfit']}
-----------
".trans('telegram.expenditure')."：-{$data['redPayTotal']}
".trans('telegram.awarding')."：+{$data['sendProfitTotal']}
-----------
".trans('telegram.bagincome')."：+{$data['getProfitTotal']}
".trans('telegram.thunderlose')."：-{$data['loseTotal']}
-----------
".trans('telegram.inviterebate')."：+{$data['todayInvite']}
".trans('telegram.shareprofit')."：+{$data['todayShare']}";
                /*
                $text.="
-----------
平台抽成：-{$result['todayPlat']}
上级代理抽成：-{$result['todayTopShare']}
Jackpot抽成：-{$result['todayJackpot']}";
                */
                $bot->answerCallbackQuery([
                    'text' => $text,
                    'show_alert' => true,
                    'cache_time' => 10
                ]);
            });
            $bot->onCallbackQueryData('share_data', function (Nutgram $bot) {
                $result = TgUserService::getShareData($bot->user()->id, $bot->chat()->id);
                $listTxt = '';
                foreach ($result['inviteUserList'] as $val) {
                    $listTxt .= ($val['first_name'] != '' ? $val['first_name'] : $val['username']) . "\n";
                }
                $bot->answerCallbackQuery([
                    'text' => trans('telegram.todayinvite')."：" . $result['todayCount'] . "
".trans('telegram.monthinvite')."：" . $result['monthCount'] . "
".trans('telegram.totalinvite')."：" . $result['totalCount'] . "
-----------
".trans('telegram.lastteninvitations')."
-----------
" . $listTxt,
                    'show_alert' => true,
                    'cache_time' => 30
                ]);
            });
            $bot->onChatMember(function (Nutgram $bot) {
                self::new_user($bot);
                return true;
            });
            /*$bot->onNewChatMembers(function (Nutgram $bot) {
                Log::info('onNewChatMembers==update：'.json_encode($bot->update()));
                $groupId = $bot->chat()->id;
                if(!$bot->message()){
                    return false;
                }
                $Member = $bot->message()->new_chat_members[0];
                if($Member){
                    $inviteTgId = !$bot->message()->from->is_bot ? $bot->message()->from->id : 0;
                    $rs = TgUserService::addUser($Member,$groupId,$inviteTgId);
                    if($rs['state'] == 1 ){
                        //欢迎语
                        $welcomeText = ConfigService::getConfigValue($groupId, 'welcome');
                        if($welcomeText){
                            $bot->sendMessage($welcomeText, ['parse_mode' => ParseMode::HTML]);
                        }
                    }
                }
            });*/



//            $bot->onLeftChatMember(function (Nutgram $bot) {
//                $groupId = $bot->chat()->id;
//                $Member = $bot->message()->left_chat_member;
//                $Member->group_id = $groupId;
//                TgUserService::leftUser($Member);
//            });


            $bot->onCommand('register(.*)', function (Nutgram $bot) {
                $groupId = $bot->chat()->id;
                $Member = $bot->user();
                $rs = TgUserService::registerUser($Member, $groupId);

                try {
                    if ($rs['state'] == 1) {
                        $bot->sendMessage(trans('telegram.registersuccess'));
                    } else {
                        $bot->sendMessage($rs['msg']);
                    }
                } catch (\Exception $e) {
                    Log::error('register异常' . $e);
                }

            });


            // Called on command "/help"
        });
    }

    public static function qiangAction($bot, $luckyid, $userId, $message_id, $callback_query_id = null)
    {
        $historyListKey = 'history_list_' . $luckyid;
        $historyListLen = Redis::llen($historyListKey);
        if ($historyListLen > 0) {
            for ($i = 0; $i < $historyListLen; $i++) {
                $json = Redis::lindex($historyListKey, $i);
                $historyObj = json_decode($json, true);
                if ($historyObj['user_id'] == $userId) {
                    if ($callback_query_id) {
                        try {
                            $bot->answerCallbackQuery([
                                'text' => trans('telegram.receivedonce',['amount'=>$historyObj['amount']]),
                                'show_alert' => true,
                                'callback_query_id' => $callback_query_id,
                                'cache_time' => 60
                            ]);
                        } catch (\Exception $e) {
                            Log::error('弹窗消息异常【您已经领取该红包，金额 ' . $historyObj['amount'] . ' U】=>' . $e->getCode() . '  msg=>' . $e->getMessage() . ' line=>' . $e->getLine());
                        }

                    }
                    Log::info('$userId='.$userId.'--$luckyid='.$luckyid.'您已经领取该红包，金额 ' . $historyObj['amount'] . ' U');
                    return false;
                }
            }
        }

        $luckyKey = 'lucky_' . $luckyid;
        $luckyInfo = Redis::get('luckyInfo_' . $luckyid);
        if (!$luckyInfo) {
            $luckyInfo = LuckyMoney::query()->where('id', $luckyid)->first();
            if (!$luckyInfo) {
                if ($callback_query_id) {
                    $bot->answerCallbackQuery([
                        'text' => trans('telegram.nodata'),
                        'show_alert' => true,
                        'callback_query_id' => $callback_query_id,
                        'cache_time' => 60
                    ]);
                }
                return false;
            }
            Redis::setex('luckyInfo_' . $luckyid, 5, serialize($luckyInfo->toArray()));
        } else {
            $luckyInfo = unserialize($luckyInfo);
        }


        $luckyNum = Redis::scard($luckyKey);
        $openNum = Redis::llen($historyListKey);
        if ($luckyNum == 0 || $openNum >= $luckyInfo['number']) {
            if ($callback_query_id) {
                try {
                    $bot->answerCallbackQuery([
                        'text' => trans('telegram.collectedall'),
                        'show_alert' => true,
                        'callback_query_id' => $callback_query_id,
                        'cache_time' => 60
                    ]);
                } catch (\Exception $e) {
                    Log::error('弹窗消息异常【该红包已全部被领取】=>' . $e->getCode() . '  msg=>' . $e->getMessage() . ' line=>' . $e->getLine());
                }
            }
            Log::info('该红包已全部被领取');
            return false;
        }

        $userInfo = TgUser::query()->where('tg_id', $userId)->where('group_id', $luckyInfo['chat_id'])->first();
        $checkRs = LuckyMoneyService::checkLuck($luckyInfo, $userInfo);
        if (!$checkRs['state']) {
            if ($callback_query_id) {
                try {
                    $bot->answerCallbackQuery([
                        'text' => $checkRs['msg'],
                        'show_alert' => true,
                        'callback_query_id' => $callback_query_id,
                    ]);
                } catch (\Exception $e) {
                    Log::error('弹窗消息异常【' . $checkRs['msg'] . '】=>' . $e->getCode() . '  msg=>' . $e->getMessage() . ' line=>' . $e->getLine());
                }

            }
            Log::info('$userId='.$userId.'--$luckyid='.$luckyid.'checkLuck=>' . $checkRs['msg']);
            return false;
        }
        if ($userInfo['pass_mine'] == 1) {
            $smembers = Redis::smembers($luckyKey);
            $redAmount = 0;
            foreach ($smembers as $sval) {
                $sval = number_format($sval, 2, '.', '');
                $isThunder = LuckyMoneyService::checkThunder($sval, $luckyInfo['thunder']);
                if (!$isThunder) {
                    $redAmount = $sval;
                    break;
                }
            }
            if ($redAmount > 0) {
                Redis::srem($luckyKey, $redAmount);
            } else {
                $redAmount = Redis::spop($luckyKey);
            }
        } else if ($userInfo['get_mine'] == 1) {
            $smembers = Redis::smembers($luckyKey);
            $redAmount = 0;
            foreach ($smembers as $sval) {
                $sval = number_format($sval, 2, '.', '');
                $isThunder = LuckyMoneyService::checkThunder($sval, $luckyInfo['thunder']);
                if ($isThunder) {
                    $redAmount = $sval;
                    break;
                }
            }
            if ($redAmount > 0) {
                Redis::srem($luckyKey, $redAmount);
            } else {
                $redAmount = Redis::spop($luckyKey);
            }
        } else {
            $redAmount = Redis::spop($luckyKey);
        }
        if (!$redAmount) {
            if ($callback_query_id) {
                try {
                    $bot->answerCallbackQuery([
                        'text' => trans('telegram.collectedall'),
                        'show_alert' => true,
                        'callback_query_id' => $callback_query_id,
                        'cache_time' => 60
                    ]);
                } catch (\Exception $e) {
                    Log::error('弹窗消息异常【该红包已全部被领取1】=>' . $e->getCode() . '  msg=>' . $e->getMessage() . ' line=>' . $e->getLine());
                }
            }
            Log::info('该红包已全部被领取:redAmount=' . $redAmount);
            return false;
        }

        $redAmount = number_format($redAmount, 2, '.', '');
        $isThunder = LuckyMoneyService::checkThunder($redAmount, $luckyInfo['thunder']);

        $loseMoney = 0;
        if ($isThunder) {
            $loseRate = ConfigService::getConfigValue($luckyInfo['chat_id'], 'lose_rate');
            $loseRate = $loseRate > 0 ? $loseRate : 1.8;
            $loseMoney = round($luckyInfo['amount'] * $loseRate, 2);
            $answerText = trans('telegram.hasthunderanswer',['redAmount'=>$redAmount,'loseMoney'=>$loseMoney]);

            $rs1 = TgUser::query()->where('tg_id', $userId)->where('group_id', $luckyInfo['chat_id'])->decrement('balance', $loseMoney);
            if (!$rs1) {
                try {
                    $bot->answerCallbackQuery([
                        'text' => '系统错误，请联系管理员',
                        'show_alert' => true,
                        'callback_query_id' => $callback_query_id,
                        'cache_time' => 60
                    ]);
                } catch (\Exception $e) {
                    Log::error('弹窗消息异常【中雷扣减数据库错误，请联系管理员】=>' . $e->getCode() . '  msg=>' . $e->getMessage() . ' line=>' . $e->getLine());
                }
                return false;
            }
            money_log($luckyInfo['chat_id'],$userId,-$loseMoney,'thunderlose','中雷损失',$luckyInfo['id']);
        } else {
            if ($luckyInfo['type'] == 1) {
                $answerText = trans('telegram.nothunderanswer',['redAmount'=>$redAmount]);
            } else {
                $answerText = trans('telegram.welfareanswer',['redAmount'=>$redAmount]);
            }
        }

        if ($callback_query_id) {
            try {
                $bot->answerCallbackQuery([
                    'text' => $answerText,
                    'show_alert' => true,
                    'callback_query_id' => $callback_query_id,
                ]);
                self::editMsg($bot, $userInfo, $luckyInfo, $isThunder, $redAmount, $loseMoney, $message_id);
            } catch (\Exception $e) {
                Redis::sadd($luckyKey, $redAmount);
                Log::error('弹窗消息异常【' . $answerText . '】=>' . $e->getCode() . '  msg=>' . $e->getMessage() . ' line=>' . $e->getLine());
            }
        } else {
            self::editMsg($bot, $userInfo, $luckyInfo, $isThunder, $redAmount, $loseMoney, $message_id);
        }

//        usleep(500000);
        return true;
    }

    public static function editMsg($bot, $userInfo, $luckyInfo, $isThunder, $redAmount, $loseMoney, $message_id)
    {
        $luckyid = $luckyInfo['id'];
        $luckyKey = 'lucky_' . $luckyid;
        $userId = $userInfo['tg_id'];
        $userName = $userInfo['first_name'] != null ? $userInfo['first_name'] : $userInfo['username'];
        $historyVal = [
            'user_id' => $userId,
            'first_name' => $userName,
            'lucky_id' => $luckyid,
            'is_thunder' => $isThunder,
            'amount' => $redAmount,
            'lose_money' => $loseMoney,
        ];
        $historyListKey = 'history_list_' . $luckyid;
        Redis::rpush($historyListKey, json_encode($historyVal));
        $luckyAmount = (int)$luckyInfo['amount'];
        $openNum = Redis::llen($historyListKey);
        $rewardCount = 0;
        $loseMoneyTotal = 0;
        $profitTotal = 0;
        Log::info('$userId='.$userId.'--$luckyid='.$luckyid.'打开数量=> ' . $openNum.';领取金额=>'.$redAmount);
        if ($luckyInfo['number'] > $openNum) {
            $titleText = trans('telegram.welfare_envelopes');
            $thunderText = '';
            $qiangText = trans('telegram.welfare_collect');
            if ($luckyInfo['type'] == 1) {
                $thunderText = trans('telegram.thunder')." {$luckyInfo['thunder']}";
                $qiangText = trans('telegram.envelopes_collect');
                $titleText = trans('telegram.envelopes');
            }

            $InlineKeyboardMarkup = InlineKeyboardMarkup::make()->addRow(
                InlineKeyboardButton::make("{$qiangText}[{$luckyInfo['number']}/{$openNum}] ".trans('telegram.total')." {$luckyAmount} U {$thunderText}", callback_data: "qiang-" . $luckyid)
            );
            $data = [
                'message_id' => $message_id,
                'caption' => "[ <code>" . format_name($luckyInfo['sender_name']) . "</code> ]" .trans('telegram.sendcaption',['amount'=>$luckyAmount]),
                'parse_mode' => ParseMode::HTML,
                'reply_markup' => common_reply_markup($luckyInfo['chat_id'], $InlineKeyboardMarkup),
                'chat_id' => $luckyInfo['chat_id']
            ];
            try {
                $bot->editMessageCaption($data);
            } catch (\Exception $e) {
                Log::error('抢包修改消息异常=>' . $e->getCode() . '  msg=>' . $e->getMessage() . ' line=>' . $e->getLine());
//                Redis::lpush($luckyKey, $redAmount);
//                Redis::rpop($historyListKey);
            }
            self::doAddHistory($bot, $luckyid, $userId, $redAmount, $isThunder, $loseMoney);
        } else {
            $details = '';

            $thunderCount = 0;

            for ($j = 1; $j <= $openNum; $j++) {
                $valJson = Redis::lindex($historyListKey, $j - 1);
                $val = json_decode($valJson, true);
                if ($val['is_thunder'] != 1) {
                    $details .= $j . ".[💵] <code>" . number_format(round($val['amount'], 2), 2, '.', '') . "</code> U <code>" . format_name($val['first_name']) . "</code>\n";
                } else {
                    $details .= $j . ".[💣] <code>" . number_format(round($val['amount'], 2), 2, '.', '') . "</code> U <code>" . format_name($val['first_name']) . "</code>\n";
                    $loseMoneyAmount = $val['lose_money'];
                    $loseMoneyTotal += $loseMoneyAmount;
                    //平台抽成
                    $platformCommission = ConfigService::getConfigValue($luckyInfo['chat_id'], 'platform_commission');
                    $platformCommissionAmount = 0;
                    if ($platformCommission > 0) {
                        $platformCommissionAmount = $loseMoneyAmount * $platformCommission / 100;
                    }
                    //jackpot抽成
                    $jackpotCommission = ConfigService::getConfigValue($luckyInfo['chat_id'], 'jackpot');
                    $jackpotAmount = 0;
                    if ($jackpotCommission > 0) {
                        $jackpotAmount = $loseMoneyAmount * $jackpotCommission / 100;
                    }
                    $senderOwn = round($loseMoneyAmount - $platformCommissionAmount - $jackpotAmount, 2);

                    //上级抽成
                    $shareRate = ConfigService::getConfigValue($luckyInfo['chat_id'], 'share_rate');
                    $shareUserId = TgUser::query()->where('tg_id', $luckyInfo['sender_id'])->where('group_id', $luckyInfo['chat_id'])->value('invite_user');

                    if($shareUserId && $shareUserId != $luckyInfo['sender_id']){
                        $shareRateAmount = $loseMoneyAmount * $shareRate / 100;
                        $senderOwn = round($senderOwn - $shareRateAmount, 2);
                    }
                    $profitTotal += $senderOwn;
                }
                if ($luckyInfo['type'] == 1  && leopard_check($val['amount'])) {
                    $rewardCount++;
                }elseif($luckyInfo['type'] == 1  && straight_check($val['amount'])) {
                    $rewardCount++;
                }
            }

            $profit = $profitTotal - $luckyInfo['amount'];
            $profitTxt = $profit >= 0 ? '+' . $profit : $profit;

            if ($luckyInfo['type'] == 1) {
                $caption = trans('telegram.collect_over',[
                    'sender_name'=>format_name($luckyInfo['sender_name']),
                    'luckyAmount'=>$luckyAmount,
                    'lose_rate'=>round($luckyInfo['lose_rate'], 2),
                    'thunder'=>$luckyInfo['thunder'],
                    'details'=>$details,
                    'loseMoneyTotal'=>$loseMoneyTotal,
                    'profitTxt'=>$profitTxt,
                    ]);

            } else {
                $caption = trans('telegram.welfare_collect_over',[
                    'sender_name'=>format_name($luckyInfo['sender_name']),
                    'luckyAmount'=>$luckyAmount,
                    'details'=>$details,
                ]);
            }
            $data = [
                'message_id' => $message_id,
                'caption' => $caption,
                'parse_mode' => ParseMode::HTML,
                'reply_markup' => common_reply_markup($luckyInfo['chat_id']),
                'chat_id' => $luckyInfo['chat_id']
            ];
            $num = 3;
            for ($i = $num; $i >= 0; $i--) {
                if ($i <= 0) {
                    Log::error('重试3次，抢包完成修改失败');
                    return false;
                }
                try {
                    $bot->editMessageCaption($data);
                    Redis::del($historyListKey);
                    self::doAddHistory($bot, $luckyid, $userId, $redAmount, $isThunder, $loseMoney);
                    if($rewardCount>=3){
                        //触发jackpot
                        self::jackpotReward($bot,$luckyInfo);
                    }
                    break;
                } catch (\Exception $e) {
                    Log::error('抢包完成修改消息异常=>' . $e->getCode() . '  msg=>' . $e->getMessage() . ' line=>' . $e->getLine());
                    if ($e->getCode() == 429) {
                        $retry_after = $e->getParameter('retry_after');
                        sleep($retry_after);
                    } else {
                        Redis::sadd($luckyKey, $redAmount);
                        Redis::rpop($historyListKey);
                        break;
                    }
                }
            }


        }
        return true;

    }
    public static function doAddHistory($bot, $luckyid, $userId, $redAmount, $isThunder, $loseMoney){
        if (env('QUEUE_CONNECTION') == 'sync') {
            self::addHistory($bot, $luckyid, $userId, $redAmount, $isThunder, $loseMoney);
        } else {
            $historyData = [
                'luckyid' => $luckyid,
                'userId' => $userId,
                'loseMoney' => $loseMoney,
                'isThunder' => $isThunder,
                'redAmount' => $redAmount,
            ];
            LuckyHistoryJob::dispatch($historyData)->onQueue('history');
        }
    }

    public static function addHistory($bot, $luckyid, $userId, $redAmount, $isThunder, $loseMoney)
    {
        $luckyInfo = LuckyMoney::query()->where('id', $luckyid)->first();
        if ($luckyInfo['status'] != 1) {
            Log::error('addHistory红包已领完或者已过期');
            return false;
        }

        $userInfo = TgUser::query()->where('tg_id', $userId)->where('group_id', $luckyInfo['chat_id'])->first();
        $userName = $userInfo['first_name'] != null ? $userInfo['first_name'] : $userInfo['username'];

        $openNum = LuckyHistory::query()->where('lucky_id', $luckyid)->count();
        $historyRs = LuckyMoneyService::addLuckyHistory($userInfo['tg_id'], $userName, $luckyInfo['id'], $isThunder, $redAmount, $loseMoney);
        if ($historyRs) {
            if ($luckyInfo['number'] <= $openNum + 1) {
                $luckyInfo->status = 2;
            }
            $luckyInfo->received = round($luckyInfo['received'] + (float)$redAmount, 2);
            $luckyInfo->received_num = $luckyInfo['received_num'] + 1;
            $rsR = $luckyInfo->save();
            if (!$rsR) {
                Log::error('save 更新失败');
                return false;
            }
        } else {
            Log::error('addLuckyHistory 领取失败');
            return false;
        }
        LuckyMoneyService::loseMoneyCal($userId, $luckyInfo, $loseMoney);
        LuckyMoneyService::getCal($userId, $luckyInfo, $redAmount);
        //判断是否是豹子
        $rewardTotal = 0;
        if ($luckyInfo['type'] == 1 && isset($redAmount) && leopard_check($redAmount)) {
            $amountCount = amount_count($redAmount);
            switch ($amountCount) {
                case 4:
                    $leopardReward = ConfigService::getConfigValue($luckyInfo['chat_id'], 'leopard_reward_4');
                    break;
                case 5:
                    $leopardReward = ConfigService::getConfigValue($luckyInfo['chat_id'], 'leopard_reward_5');
                    break;
                case 3:
                default:
                    $leopardReward = ConfigService::getConfigValue($luckyInfo['chat_id'], 'leopard_reward');
                    break;
            }
            if ($leopardReward > 0) {
                $rewardTotal += $leopardReward;
                $bot->sendMessage(trans('telegram.leopard_reward',['userName'=>$userName,'redAmount'=>$redAmount,'leopardReward'=>$leopardReward]), ['chat_id' => $luckyInfo['chat_id'], 'parse_mode' => ParseMode::HTML]);
                LuckyMoneyService::addRewardRecord($luckyid, $luckyInfo['sender_id'], $userId, $luckyInfo['chat_id'], $leopardReward, $redAmount, 1);
            }
        }
        //判断是否是顺子
        if ($luckyInfo['type'] == 1 && isset($redAmount) && straight_check($redAmount)) {
            $amountCount = amount_count($redAmount);
            switch ($amountCount) {
                case 4:
                    $straightReward = ConfigService::getConfigValue($luckyInfo['chat_id'], 'straight_reward_4');
                    break;
                case 5:
                    $straightReward = ConfigService::getConfigValue($luckyInfo['chat_id'], 'straight_reward_5');
                    break;
                case 3:
                default:
                    $straightReward = ConfigService::getConfigValue($luckyInfo['chat_id'], 'straight_reward');
                    break;
            }
            if ($straightReward > 0) {
                $rewardTotal += $straightReward;
                $bot->sendMessage(trans('telegram.straight_reward',['userName'=>$userName,'redAmount'=>$redAmount,'straightReward'=>$straightReward]), ['chat_id' => $luckyInfo['chat_id'], 'parse_mode' => ParseMode::HTML]);
                LuckyMoneyService::addRewardRecord($luckyid, $luckyInfo['sender_id'], $userId, $luckyInfo['chat_id'], $straightReward, $redAmount, 2);
            }
        }
        if($rewardTotal>0){
            $userInfo->balance = $userInfo->balance + $rewardTotal;
            $userInfo->save();
            money_log($luckyInfo['chat_id'],$userId,$rewardTotal,'reward','顺子/豹子中奖盈利',$luckyInfo['id']);
        }

        return true;
    }
    //奖池分配
    public static function jackpotReward($bot, $luckyInfo)
    {
        $jackpotCommission = ConfigService::getConfigValue($luckyInfo['chat_id'], 'jackpot');
        if($jackpotCommission>0){
            $jackInfo = JackpotPool::query()->where('group_id',$luckyInfo['chat_id'])->first();
            if($jackInfo['balance'] > 0){

                $ls = LuckyHistory::query()->where('lucky_id',$luckyInfo['id'])->get();
                $userIds = array_column($ls->toArray(),'user_id');
                $rewardAmount = $jackInfo['balance'] * 0.4;
                $text = trans('telegram.jackpot_reward',['rewardAmount'=>$rewardAmount]);
                //奖池减少
                JackpotPool::query()->where('group_id',$luckyInfo['chat_id'])->decrement('balance',$rewardAmount);
                $senderAmount = round($rewardAmount/2,2);

                $user = TgUser::query()->where('tg_id',$luckyInfo['sender_id'])->first();
                $user->balance = $user->balance + $senderAmount;
                $user->save();
                $userName = $user['first_name']?$user['first_name']:$user['username'];
                $text .= "💵{$senderAmount} <code>{$userName}</code>\n";
                //记录
                JackpotReward::query()->create([
                    'lucky_id'=>$luckyInfo['id'],
                    'amount'=>$senderAmount,
                    'tg_id'=>$luckyInfo['sender_id'],
                    'group_id'=>$luckyInfo['chat_id'],
                    'sender_id'=>$luckyInfo['sender_id']
                ]);
                money_log($luckyInfo['chat_id'],$luckyInfo['sender_id'],$senderAmount,'jacpotprofit','jackpot发包中奖',$luckyInfo['id']);

                $averageAmount = round($senderAmount/count($userIds),2);

                //每个用户奖励
                foreach ($userIds as $userId){
                    $user = TgUser::query()->where('tg_id',$userId)->first();
                    $user->balance = $user->balance + $averageAmount;
                    $user->save();
                    $userName = $user['first_name']?$user['first_name']:$user['username'];
                    $text .= "💵{$averageAmount} <code>{$userName}</code>\n";
                    //记录
                    JackpotReward::query()->create([
                        'lucky_id'=>$luckyInfo['id'],
                        'amount'=>$averageAmount,
                        'tg_id'=>$userId,
                        'group_id'=>$luckyInfo['chat_id'],
                        'sender_id'=>$luckyInfo['sender_id']
                    ]);
                    money_log($luckyInfo['chat_id'],$luckyInfo['sender_id'],$averageAmount,'jacpotprofit','jackpot中奖',$luckyInfo['id']);
                }
                $text .= trans('telegram.jackpot_bonus_send');

                $bot->sendMessage($text, ['chat_id' => $luckyInfo['chat_id'], 'parse_mode' => ParseMode::HTML]);
            }
        }
    }

    public static function shangfen($bot,$amount){
        $from = $bot->message()->from->id;
        $finance = ConfigService::getConfigValue($bot->chat()->id, 'finance');
        $financeArr = explode(',', $finance);
        if (!in_array($from, $financeArr)) {
            return false;
        }
        $params = ['parse_mode' => ParseMode::HTML];
        if ($amount < 1 || !$amount) {
            $bot->sendMessage(trans('telegram.amouterror'), $params);
            return false;
        }
        $reply_to_message = $bot->message()->reply_to_message;
        if (!$reply_to_message) {
            return false;
        }
        if ($reply_to_message->from->is_bot == true) {
            return false;
        }
        $username = $reply_to_message->from->first_name != '' ? $reply_to_message->from->first_name : $reply_to_message->from->username;
        $tgId = $reply_to_message->from->id;
        $user = TgUser::query()->where('tg_id', $tgId)->where('group_id', $bot->chat()->id)->first();
        if (!$user) {
            TgUserService::registerUser($reply_to_message->from, $bot->chat()->id);
            $user = TgUser::query()->where('tg_id', $tgId)->where('group_id', $bot->chat()->id)->first();
        }
        $balance = $user->balance + $amount;
        try {
            DB::beginTransaction();
            $user->balance = $balance;
            $rs = $user->save();
            if ($rs) {
                money_log($user->group_id, $user->tg_id, $amount, 'recharge', '财务上分');
                $insert = [
                    'tg_id' => $user->tg_id,
                    'username' => $user->username,
                    'first_name' => $user->first_name,
                    'group_id' => $user->group_id,
                    'amount' => $amount,
                    'remark' => '财务上分',
                    'status' => 1,
                    'type' => 3,//财务群聊上分
                    'admin_id' => 0,
                ];
                $rs2 = RechargeRecord::query()->create($insert);
                if (!$rs2) {
                    DB::rollBack();
                    $bot->sendMessage(trans('telegram.rechargefailed'), $params);
                    return false;
                }
                DB::commit();
                $bot->sendMessage(trans('telegram.rechargemsg',['amount'=>$amount,'username'=>$username,'tgId'=>$tgId,'balance'=>$balance]), $params);
            }
        } catch (\Exception $e) {
            Log::error('上分异常:' . $e->getMessage() . ' code=>' . $e->getCode());
            $bot->sendMessage(trans('telegram.rechargefailed'), $params);
        }
    }

    public static function xiafen($bot,$amount){
        $from = $bot->message()->from->id;
        $finance = ConfigService::getConfigValue($bot->chat()->id, 'finance');
        $financeArr = explode(',', $finance);
        if (!in_array($from, $financeArr)) {
            return false;
        }
        $params = ['parse_mode' => ParseMode::HTML];
        if ($amount < 1 || !$amount) {
            $bot->sendMessage(trans('telegram.amouterror'), $params);
            return false;
        }
        $reply_to_message = $bot->message()->reply_to_message;
        if (!$reply_to_message) {
            return false;
        }
        if ($reply_to_message->from->is_bot == true) {
            return false;
        }
        $username = $reply_to_message->from->first_name != '' ? $reply_to_message->from->first_name : $reply_to_message->from->username;
        $tgId = $reply_to_message->from->id;
        $user = TgUser::query()->where('tg_id', $tgId)->where('group_id', $bot->chat()->id)->first();
        if (!$user || $amount > $user->balance) {
            $bot->sendMessage(trans('telegram.nobalance'), $params);
            return false;
        }
        $balance = $user->balance - $amount;
        try {
            DB::beginTransaction();
            $user->balance = $balance;
            $rs = $user->save();
            if ($rs) {
                money_log($user->group_id, $user->tg_id, -$amount, 'withdraw', '财务下分');
                $insert = [
                    'tg_id' => $user->tg_id,
                    'username' => $user->username,
                    'first_name' => $user->first_name,
                    'group_id' => $user->group_id,
                    'amount' => $amount,
                    'remark' => '财务下分',
                    'status' => 1,
                    'address' => '',
                    'addr_type' => '',
                    'admin_id' => 0,
                ];
                $rs2 = WithdrawRecord::query()->create($insert);
                if (!$rs2) {
                    DB::rollBack();
                    $bot->sendMessage(trans('telegram.withdrawfailed'), $params);
                    return false;
                }
                DB::commit();
                $bot->sendMessage(trans('telegram.withdrawmsg',['amount'=>$amount,'username'=>$username,'tgId'=>$tgId,'balance'=>$balance]), $params);
            }
        } catch (\Exception $e) {
            Log::error('下分异常:' . $e->getMessage() . ' code=>' . $e->getCode());
            $bot->sendMessage(trans('telegram.withdrawfailed'), $params);
        }
    }
    public static function fabao($bot, $ac, $amount, $mine,$chatId='',$sendUserId='')
    {
        $chatId = $chatId ? $chatId : $bot->chat()->id;
        $pattern = '/^\d+\.\d+?$/';
        if (preg_match($pattern, $amount)) {
            $bot->sendMessage(trans('telegram.commanderror_integer'));
            return false;
        }
        if (preg_match($pattern, $mine)) {
            $bot->sendMessage(trans('telegram.commanderror_integer'));
            return false;
        }
        if ($mine > 9 || $mine < 0 || $mine == null) {
            $bot->sendMessage(trans('telegram.commanderror_thundernum'));
            return false;
        }
        $minAmount = ConfigService::getConfigValue($chatId, 'min_amount');
        if ($amount < $minAmount) {
            $bot->sendMessage(trans('telegram.error_lessthan',['minAmount'=>$minAmount]));
            return false;
        }
        $maxAmount = ConfigService::getConfigValue($chatId, 'max_amount');
        if ($amount > $maxAmount) {
            $bot->sendMessage(trans('telegram.error_greaterthan',['maxAmount'=>$maxAmount]));
            return false;
        }
        $amount = (int)$amount;
        $mine = (int)$mine;
        $sendUserId = $sendUserId?$sendUserId:$bot->user()->id;
        //检查是否还有包为完成
//        $checkUnFinish = LuckyMoneyService::checkLuckyUnfinish($chatId, $sendUserId);
//        if (!$checkUnFinish) {
//            $bot->sendMessage('您发的包尚未被抢完 请等待抢完/过期');
//            return false;
//        }

        $senderInfo = TgUser::query()->where('tg_id', $sendUserId)->where('group_id', $chatId)->first();
        if(!$senderInfo){
            $bot->sendMessage(trans('telegram.notregistered'));
            return false;
        }
        $senderName = $senderInfo['first_name']?$senderInfo['first_name']:$senderInfo['username'];
        //检查余额
        $checkRs = TgUserService::checkBalance($senderInfo, $amount);
        if ($checkRs['state'] != 1) {
            try {
                $bot->sendMessage($checkRs['msg']);
            } catch (\Exception $e) {
                $bot->sendMessage($checkRs['msg']);
            }
            return false;
        }

        $luckyTotal = ConfigService::getConfigValue($chatId, 'lucky_num');
        DB::beginTransaction();
        //添加红包
        $luckyId = LuckyMoneyService::addLucky($senderInfo, $senderName, $amount, $mine, $chatId, $luckyTotal, 0);
        if (!$luckyId) {
            DB::rollBack();
            $bot->sendMessage(trans('telegram.failedtosend'));
            return false;
        }
        $photo = get_photo($chatId);
        if (!$photo) {
            $bot->sendMessage(trans('telegram.nopicture'));
            DB::rollBack();
            return false;
        }
        $num = 3;
        for ($i = $num; $i >= 0; $i--) {
            if ($i <= 0) {
                Log::error('重试3次，发送红包失败');
                DB::rollBack();
                return false;
            }
            try {
                $InlineKeyboardMarkup = InlineKeyboardMarkup::make()->addRow(
                    InlineKeyboardButton::make(trans('telegram.firstbtntext',['luckyTotal'=>$luckyTotal,'amount'=>$amount,'mine'=>$mine]), callback_data: "qiang-" . $luckyId)
                );
                $data = [
                    'chat_id'=>$chatId,
                    'caption' => "[ <code>" . format_name($senderName) . "</code> ]".trans('telegram.sendcaption',['amount'=>$amount]),
                    'parse_mode' => ParseMode::HTML,
                    'reply_markup' => common_reply_markup($chatId, $InlineKeyboardMarkup),
                ];
                $sendRs = $bot->sendPhoto($photo, $data);
                if ($sendRs) {
                    $updateRs = LuckyMoney::query()->where('id', $luckyId)->update(['message_id' => $sendRs->message_id]);
                    if (!$updateRs) {
                        DB::rollBack();
                        return false;
                    }
                    DB::commit();
                    break;
                } else {
                    DB::rollBack();
                    Log::error('sendPhoto发送失败');
                    $bot->sendMessage(trans('telegram.failedtosend'));
//                    LuckyMoneyService::delLucky($luckyId,$chatId,$sendUserId,$amount);
                }
            } catch (\Exception $e) {
                Log::error('红包发送失败=>code=>' . $e->getCode() . '  msg=>' . $e->getMessage());
                if ($e->getCode() == 429) {
                    $retry_after = $e->getParameter('retry_after');
                    sleep($retry_after);
                } else {
                    DB::rollBack();
//                    LuckyMoneyService::delLucky($luckyId,$chatId,$sendUserId,$amount);
                    break;
                }
                //throw new TelegramException($e->getMessage(),$e->getCode());
            }
        }
    }

    public static function fuli($bot, $amount, $num){
            $chatId = $bot->chat()->id;
            $pattern = '/^\d+\.\d+?$/';
            if (preg_match($pattern, $amount)) {
                $bot->sendMessage(trans('telegram.commanderror_integer'));
                return false;
            }
            if (preg_match($pattern, $num)) {
                $bot->sendMessage(trans('telegram.commanderror_integer'));
                return false;
            }
            if ($num < 2 || $num > 100) {
                $bot->sendMessage(trans('telegram.welfarelimit'));
                return false;
            }
            if ($amount < 1) {
                $bot->sendMessage(trans('telegram.welfarelimit'));
                return false;
            }
            if ($amount / $num < 0.1) {
                $bot->sendMessage(trans('telegram.welfaretoomany'));
                return false;
            }
            $num = (int)$num;
            $amount = (int)$amount;
            $sendUserId = $bot->user()->id;
            $senderInfo = TgUser::query()->where('tg_id', $sendUserId)->where('group_id', $chatId)->first();
            $senderName = $senderInfo['first_name']?$senderInfo['first_name']:$senderInfo['username'];
            //检查余额
            $checkRs = TgUserService::checkBalance($senderInfo, $amount);
            if ($checkRs['state'] != 1) {
                try {
                    $bot->sendMessage($checkRs['msg']);
                } catch (\Exception $e) {
                    $bot->sendMessage($checkRs['msg']);
                }
            } else {
                try {
                    DB::beginTransaction();
                    //添加红包
                    $luckyId = LuckyMoneyService::addLucky($senderInfo, $senderName, $amount, 0, $chatId, $num, 0, 2);
                    if ($luckyId) {
                        $photo = get_photo($chatId);
                        if (!$photo) {
                            DB::rollBack();
                            $bot->sendMessage(trans('telegram.nopicture'));
                            return false;
                        }
                        $InlineKeyboardMarkup = InlineKeyboardMarkup::make()->addRow(
                            InlineKeyboardButton::make(trans('telegram.welfarefirstbtntext',['num'=>$num,'amount'=>$amount]), callback_data: "qiang-" . $luckyId)
                        );
                        $data = [
                            'caption' => "[ <code>" . format_name($senderName) . "</code> ]".trans('telegram.welfaresendcaption',['amount'=>$amount]),
                            'parse_mode' => ParseMode::HTML,
                            'reply_markup' => common_reply_markup($chatId, $InlineKeyboardMarkup),
                        ];
                        $sendRs = $bot->sendPhoto($photo, $data);
                        if ($sendRs) {
                            $updateRs = LuckyMoney::query()->where('id', $luckyId)->update(['message_id' => $sendRs->message_id]);
                            if (!$updateRs) {
                                DB::rollBack();
                                return false;
                            }
                            DB::commit();
                        } else {
                            DB::rollBack();
                            $bot->sendMessage(trans('telegram.failedtosend'));
                        }
                    } else {
                        DB::rollBack();
                        $bot->sendMessage(trans('telegram.failedtosend'));
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                }
            }



    }

    public static function new_user($bot){
        $groupId = $bot->chat()->id;
        $status = $bot->chatMember()->new_chat_member->status;
        if ($status != 'member') {
            return false;
        }
        $memberInfo = $bot->chatMember()->new_chat_member->user;
        if (!$memberInfo) {
            return false;
        }
        $inviteTgId = 0;
        if (isset($bot->chatMember()->from) && $bot->chatMember()->from->id != $memberInfo->id) {
            $inviteTgId = $bot->chatMember()->from->id;
        }
        if ($bot->chatMember()->invite_link) {
            $inviteTgId = InviteLink::query()->where('invite_link', $bot->chatMember()->invite_link->invite_link)->value('tg_id');
        }

        $rs = TgUserService::addUser($memberInfo, $groupId, $inviteTgId);
        if ($rs['state'] == 1) {
            //欢迎语
            $welcomeText = ConfigService::getConfigValue($groupId, 'welcome');
            if ($welcomeText) {
                try {
                    $userName = $memberInfo->first_name?$memberInfo->first_name:$memberInfo->username;
                    $welcomeText = str_replace('{NAME}',$userName,$welcomeText);
                    $bot->sendMessage($welcomeText, ['parse_mode' => ParseMode::HTML]);
                } catch (\Exception $e) {
                    Log::error('onChatMember异常' . $e);
                }

            }
        }
    }
    public static function cha($bot){
        $reply_to_message = $bot->message()->reply_to_message;
        if (!$reply_to_message) {
            $userInfo = TgUserService::getUserById($bot->user()->id, $bot->chat()->id);
        }else{
            $from = $bot->message()->from->id;
            $finance = ConfigService::getConfigValue($bot->chat()->id, 'finance');
            $financeArr = explode(',', $finance);
            if (!in_array($from, $financeArr)) {
                return false;
            }
            $userInfo = TgUserService::getUserById($reply_to_message->from->id, $bot->chat()->id);
        }

        $params = [];
        if ($bot->message()->message_id) {
            $params = ['parse_mode' => ParseMode::HTML];
        }
        try {
            try {
                if (!$userInfo) {
                    $bot->sendMessage(trans('telegram.notregistered'), $params);
                } else {
                    $username = $userInfo->first_name?$userInfo->first_name:$userInfo->username;
                    $bot->sendMessage("💰[ {$username} ] ".trans('telegram.balance')."：{$userInfo->balance}  U", $params);
                }
            } catch (\Exception $e) {
                if (!$userInfo) {
                    $bot->sendMessage(trans('telegram.notregistered'));
                } else {
                    $username = $userInfo->first_name?$userInfo->first_name:$userInfo->username;
                    $bot->sendMessage("💰[ {$username} ] ".trans('telegram.balance')."：{$userInfo->balance}  U");
                }
            }

        } catch (\Exception $e) {
            Log::error('查询余额异常' . $e);
        }
    }

    public static function invite_link($bot): void
    {
        $chatId = $bot->chat()->id;
        $params = [
            'parse_mode' => ParseMode::HTML
        ];

        if (\App\Models\AuthGroup::query()->where('group_id', $bot->chat()->id)->count() == 0) {
            $user = \App\Models\TgUser::query()->where('tg_id', $bot->user()->id)->orderBy('id', 'desc')->first();
            if (!$user) {
                $bot->sendMessage(trans('telegram.invite_err1'),$params);

                return;
            }
            $chatId = $user->group_id;
        }
        $rs = $bot->createChatInviteLink($chatId);
        if (!$rs->invite_link) {
            $bot->sendMessage(trans('telegram.invite_err2'),$params);

            return;
        }
        if (InviteLink::query()->where('group_id', $chatId)->where('invite_link', $rs->invite_link)->where('tg_id', $bot->user()->id)->count() == 0) {
            $insert = [
                'tg_id' => $bot->user()->id,
                'group_id' => $chatId,
                'invite_link' => $rs->invite_link,
            ];
            $iRs = InviteLink::query()->create($insert);
            if (!$iRs) {
                try{
                    $bot->sendMessage(trans('telegram.invite_err2'),$params);
                } catch (\Exception $e) {
                    $bot->sendMessage(trans('telegram.invite_err2'), ['parse_mode' => ParseMode::HTML]);
                }
                return;
            }
        }
        $userName = $bot->user()->first_name?$bot->user()->first_name:$bot->user()->username;
        $bot->sendMessage(trans('telegram.invite_link',['invite_link'=>$rs->invite_link,'username'=>$userName]),$params);
    }
}
