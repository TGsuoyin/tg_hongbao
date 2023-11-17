<?php

namespace App\Services;

use App\Models\CommissionRecord;
use App\Models\InviteRecord;
use App\Models\JackpotRecord;
use App\Models\LuckyHistory;
use App\Models\LuckyMoney;
use App\Models\MoneyLog;
use App\Models\ShareRecord;
use App\Models\TgUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * author [@cody](https://t.me/cody0512)
 */
class TgUserService
{

    public function __construct()
    {

    }

    public static function addUser($memberInfo,$groupId,$inviteUserId = 0)
    {
        $info = TgUser::query()->where('tg_id', $memberInfo->id)->where('group_id', $groupId)->first();
        if (!$info) {
            $default_balance = ConfigService::getConfigValue($groupId, 'default_balance');
            $insert = [
                'username' => $memberInfo->username,
                'first_name' => $memberInfo->first_name,
                'tg_id' => $memberInfo->id,
                'group_id' => $groupId,
                'balance' => $default_balance > 0 ? $default_balance : 0,
                'status' => 1,
                'invite_user' => 0,
            ];
            if ($inviteUserId && $inviteUserId != $memberInfo->id ) {
                $insert['invite_user'] = $inviteUserId;
            }
            $rs = TgUser::query()->create($insert);
            if (!$rs) {
                return ['state' => 0, 'msg' => trans('telegram.registerfailed')];
            }
            if ($inviteUserId && $inviteUserId != $memberInfo->id) {
                self::addInviteAmount($inviteUserId, $memberInfo->id, $groupId);
            }
            return ['state' => 1];
        } else {
            if ($inviteUserId && !$info->invite_user) {
                $info->invite_user = $inviteUserId;
                $rs = $info->save();
                if ($rs && $inviteUserId != $memberInfo->id) {
                    self::addInviteAmount($inviteUserId, $memberInfo->id, $groupId);
                }
                if (!$rs) {
                    return ['state' => 0, 'msg' => trans('telegram.registerfailed')];
                }
            }

            return ['state' => 2];
        }

    }

    public static function registerUser($memberInfo,$groupId)
    {
        $info = TgUser::query()->where('tg_id', $memberInfo->id)->where('group_id', $groupId)->first();
        if (!$info) {
            $default_balance = ConfigService::getConfigValue($groupId, 'default_balance');
            $insert = [
                'username' => $memberInfo->username,
                'first_name' => $memberInfo->first_name,
                'tg_id' => $memberInfo->id,
                'group_id' => $groupId,
                'balance' =>  $default_balance > 0 ? $default_balance : 0,
                'status' => 1,
                'invite_user' => 0,
            ];
            $rs = TgUser::query()->create($insert);
            if (!$rs) {
                return ['state' => 0, 'msg' => trans('telegram.registerfailed')];
            }
        } else if ($info['status'] == 0) {
            $info->status = 1;
            $rs = $info->save();
            if (!$rs) {
                return ['state' => 0, 'msg' => trans('telegram.registerfailed')];
            }
        } else if ($info['status'] == 1) {
            return ['state' => 0, 'msg' => trans('telegram.userregistered')];
        }
        return ['state' => 1];
    }

    public static function addInviteAmount($inviteUserId, $tg_id, $group_id)
    {
        $inviteCount = InviteRecord::query()->where('invite_user_id', $inviteUserId)->where('group_id', $group_id)->where('tg_id', $tg_id)->count();
        if ($inviteCount) {
            return true;
        }

        $amount = ConfigService::getConfigValue($group_id, 'invite_usdt');
        if($amount>0){
            $rs = TgUser::query()->where('tg_id', $inviteUserId)->where('group_id', $group_id)->increment('balance', $amount);
            if ($rs) {
                $insert = [
                    'amount' => $amount,
                    'tg_id' => $tg_id,
                    'group_id' => $group_id,
                    'invite_user_id' => $inviteUserId,
                    'remark' => '邀请返利',
                ];
                $rsCreate = InviteRecord::query()->create($insert);
                if (!$rsCreate) {
                    return false;
                }
                money_log($group_id,$inviteUserId,$amount,'invite','邀请返利');
                return true;
            }else{
            }
        }
        return true;
    }

    public static function leftUser($memberInfo)
    {
        $info = TgUser::query()->where('tg_id', $memberInfo->id)->where('group_id', $memberInfo->group_id)->first();
        if ($info && $info['status'] == 1) {
            $info->status = 0;
            $info->save();
        }
    }

    public static function checkBalance($senderInfo, $amount)
    {
        if (!$senderInfo) {
            return ['state' => 0, 'msg' => trans('telegram.notregistered')];
        } else if (!$senderInfo->status) {
            return ['state' => 0, 'msg' => trans('telegram.userbanned')];
        } else if ($senderInfo->balance < $amount) {
            return ['state' => 0, 'msg' => trans('telegram.insufficientbalance')];
        }
        return ['state' => 1];
    }

    public static function getUserById($id, $groupId)
    {
        return TgUser::query()->where('tg_id', $id)->where('group_id', $groupId)->first();

    }

    public static function getShareData($id, $chatId)
    {
        $key = 'share_'.$id.'_'.$chatId;
        $shareData = Cache::get($key);
        if(!$shareData) {
            $todayCount = TgUser::query()->where('invite_user', $id)->where('group_id', $chatId)
                ->where('created_at', '>=', Carbon::now()->startOfDay())
                ->where('created_at', '<=', Carbon::now()->endOfDay())
                ->count();

            $monthCount = TgUser::query()->where('invite_user', $id)->where('group_id', $chatId)
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->where('created_at', '<=', Carbon::now()->endOfMonth())
                ->count();

            $totalCount = TgUser::query()->where('invite_user', $id)->where('group_id', $chatId)
                ->count();
            $inviteUserList = TgUser::query()->where('invite_user', $id)->where('group_id', $chatId)->limit(10)->orderBy('id', 'desc')
                ->get();
            if (!$inviteUserList->isEmpty()) {
                $inviteUserList = $inviteUserList->toArray();
            } else {
                $inviteUserList = [];
            }
            $return = [
                'todayCount' => $todayCount,
                'monthCount' => $monthCount,
                'totalCount' => $totalCount,
                'inviteUserList' => $inviteUserList,
            ];
            Cache::set($key,serialize($return),10);
        }else{
            $return = unserialize($shareData);
        }
        return $return;
    }
    public static function getYesterdayData($id, $chatId)
    {
        $key = 'today_'.$id.'_'.$chatId;
        $todayData = Cache::get($key);
        if(!$todayData){
            $info = TgUser::query()->where('tg_id', $id)->where('group_id', $chatId)->first();
            if (!$info) {
                return ['state' => 0, 'msg' => trans('telegram.notregistered')];
            } else if (!$info->status) {
                return ['state' => 0, 'msg' => trans('telegram.userbanned')];
            }
            $todayStart = Carbon::yesterday()->startOfDay();
            $todayEnd = Carbon::yesterday()->endOfDay();
            //红包支出
            $redPayTotal = LuckyMoney::query()->where('sender_id', $id)->where('chat_id', $chatId)
                ->where('created_at', '>=', $todayStart)->where('created_at', '<', $todayEnd)->sum('received');
            //发包盈收
            $sendProfitTotal = LuckyHistory::query()->where('lucky_history.created_at', '>=', $todayStart)->where('lucky_history.created_at', '<', $todayEnd)
                ->leftJoin('lucky_money as lm', 'lm.id', '=', 'lucky_history.lucky_id')
                ->where('lm.sender_id', $id)->where('lm.chat_id', $chatId)->sum('lucky_history.lose_money');
            //抢包收入
            $getProfitTotal = LuckyHistory::query()->where('created_at', '>=', $todayStart)->where('created_at', '<', $todayEnd)
                ->where('user_id', $id)->sum('amount');
            $loseTotal = LuckyHistory::query()->where('created_at', '>=', $todayStart)->where('created_at', '<', $todayEnd)
                ->where('user_id', $id)->sum('lose_money');

            //邀请返利
            $todayInvite = InviteRecord::query()->where('group_id', $chatId)->where('invite_user_id', $id)
                ->where('created_at', '>=', $todayStart)->where('created_at', '<', $todayEnd)
                ->sum('amount');
            //下级返利
            $todayShare = ShareRecord::query()->where('group_id', $chatId)->where('share_user_id', $id)
                ->where('created_at', '>=', $todayStart)->where('created_at', '<', $todayEnd)
                ->sum('amount');

            //我发包玩家中雷上级代理抽成
//            $todayTopShare = ShareRecord::query()->where('group_id', $chatId)->where('sender_id', $id)
//                ->where('created_at', '>=', $todayStart)->where('created_at', '<', $todayEnd)
//                ->sum('amount');
//
//            //我发包玩家中雷平台抽成
//            $todayPlat = CommissionRecord::query()->where('group_id', $chatId)->where('sender_id', $id)
//                ->where('created_at', '>=', $todayStart)->where('created_at', '<', $todayEnd)
//                ->sum('amount');
//
//            //jackpot奖池抽成
//            $todayJackpot = JackpotRecord::query()->where('group_id', $chatId)->where('sender_id', $id)
//                ->where('created_at', '>=', $todayStart)->where('created_at', '<', $todayEnd)
//                ->sum('amount');

            $todayProfit = $sendProfitTotal+$todayInvite+$getProfitTotal+$todayShare - $redPayTotal  - $loseTotal;
            //$todayProfit =$todayProfit - $todayTopShare - $todayPlat - $todayJackpot;
            $return = [
                'redPayTotal' => round($redPayTotal, 2),
                'sendProfitTotal' => round($sendProfitTotal, 2),
                'getProfitTotal' => round($getProfitTotal, 2),
                'loseTotal' => round($loseTotal, 2),

                'todayShare' => round($todayShare, 2),
                'todayInvite' => round($todayInvite, 2),
//                'todayPlat' => round($todayPlat, 2),
//                'todayTopShare' => round($todayTopShare, 2),
//                'todayJackpot' => round($todayJackpot, 2),
                'todayProfit' => $todayProfit > 0 ? '+' . round($todayProfit, 2) : round($todayProfit, 2),
            ];
            Cache::set($key,serialize($return),10);
        }else{
            $return = unserialize($todayData);

        }

        return ['state'=>1,'data'=>$return];
    }

    public static function getTodayData($id, $chatId)
    {
        $key = 'today_'.$id.'_'.$chatId;
        $todayData = Cache::get($key);
        if(!$todayData){
            $info = TgUser::query()->where('tg_id', $id)->where('group_id', $chatId)->first();
            if (!$info) {
                return ['state' => 0, 'msg' => trans('telegram.notregistered')];
            } else if (!$info->status) {
                return ['state' => 0, 'msg' => trans('telegram.userbanned')];
            }
            $todayStart = date('Y-m-d 00:00:00');
            $todayEnd = date('Y-m-d H:i:s');
            //红包支出
            $redPayTotal = LuckyMoney::query()->where('sender_id', $id)->where('chat_id', $chatId)
                ->where('created_at', '>=', $todayStart)->where('created_at', '<', $todayEnd)->sum('received');
            //发包盈收
            $sendProfitTotal = LuckyHistory::query()->where('lucky_history.created_at', '>=', $todayStart)->where('lucky_history.created_at', '<', $todayEnd)
                ->leftJoin('lucky_money as lm', 'lm.id', '=', 'lucky_history.lucky_id')
                ->where('lm.sender_id', $id)->where('lm.chat_id', $chatId)->sum('lucky_history.lose_money');
            //抢包收入
            $getProfitTotal = LuckyHistory::query()->where('created_at', '>=', $todayStart)->where('created_at', '<', $todayEnd)
                ->where('user_id', $id)->sum('amount');
            $loseTotal = LuckyHistory::query()->where('created_at', '>=', $todayStart)->where('created_at', '<', $todayEnd)
                ->where('user_id', $id)->sum('lose_money');

            //邀请返利
            $todayInvite = InviteRecord::query()->where('group_id', $chatId)->where('invite_user_id', $id)
                ->where('created_at', '>=', $todayStart)->where('created_at', '<', $todayEnd)
                ->sum('amount');
            //下级返利
            $todayShare = ShareRecord::query()->where('group_id', $chatId)->where('share_user_id', $id)
                ->where('created_at', '>=', $todayStart)->where('created_at', '<', $todayEnd)
                ->sum('amount');

            //我发包玩家中雷上级代理抽成
//            $todayTopShare = ShareRecord::query()->where('group_id', $chatId)->where('sender_id', $id)
//                ->where('created_at', '>=', $todayStart)->where('created_at', '<', $todayEnd)
//                ->sum('amount');
//
//            //我发包玩家中雷平台抽成
//            $todayPlat = CommissionRecord::query()->where('group_id', $chatId)->where('sender_id', $id)
//                ->where('created_at', '>=', $todayStart)->where('created_at', '<', $todayEnd)
//                ->sum('amount');
//
//            //jackpot奖池抽成
//            $todayJackpot = JackpotRecord::query()->where('group_id', $chatId)->where('sender_id', $id)
//                ->where('created_at', '>=', $todayStart)->where('created_at', '<', $todayEnd)
//                ->sum('amount');

            $todayProfit = $sendProfitTotal+$todayInvite+$getProfitTotal+$todayShare - $redPayTotal  - $loseTotal;
            //$todayProfit =$todayProfit - $todayTopShare - $todayPlat - $todayJackpot;
            $return = [
                'redPayTotal' => round($redPayTotal, 2),
                'sendProfitTotal' => round($sendProfitTotal, 2),
                'getProfitTotal' => round($getProfitTotal, 2),
                'loseTotal' => round($loseTotal, 2),

                'todayShare' => round($todayShare, 2),
                'todayInvite' => round($todayInvite, 2),
//                'todayPlat' => round($todayPlat, 2),
//                'todayTopShare' => round($todayTopShare, 2),
//                'todayJackpot' => round($todayJackpot, 2),
                'todayProfit' => $todayProfit > 0 ? '+' . round($todayProfit, 2) : round($todayProfit, 2),
            ];
            Cache::set($key,serialize($return),10);
        }else{
            $return = unserialize($todayData);

        }

        return ['state'=>1,'data'=>$return];
    }
    public static function getTeamData($id, $chatId){
        $start_date=Carbon::now()->startOfDay();
        $end_date = Carbon::now();
        $users = TgUser::query()->where('invite_user',$id)->select('tg_id')->get();
        if($users->isEmpty()){
            $users = $users->toArray();
        }else{
            $users = [];
        }
        $userIds = array_column($users,'tg_id');
        $todayProfit = MoneyLog::query()->where('created_at','>',Carbon::parse($start_date))
            ->whereIn('tg_id',$userIds)
            ->where('group_id',$chatId)
            ->whereNotIn('type',['recharge','withdraw'])
            ->where('created_at','<',Carbon::parse($end_date))->sum('amount');


        $todayRecharge = MoneyLog::query()->where('created_at','>',Carbon::parse($start_date))
            ->whereIn('tg_id',$userIds)
            ->where('group_id',$chatId)
            ->where('type','recharge')
            ->where('created_at','<',Carbon::parse($end_date))->sum('amount');

        $todayWithdraw = MoneyLog::query()->where('created_at','>',Carbon::parse($start_date))
            ->whereIn('tg_id',$userIds)
            ->where('group_id',$chatId)
            ->where('type','withdraw')
            ->where('created_at','<',Carbon::parse($end_date))->sum('amount');

        $todaySendAmount = MoneyLog::query()->where('created_at','>',Carbon::parse($start_date))
            ->whereIn('tg_id',$userIds)
            ->where('group_id',$chatId)
            ->where('type','sendbag')
            ->where('created_at','<',Carbon::parse($end_date))->sum('amount');

        $return = [
            'todayProfit' => round($todayProfit, 2),
            'todayRecharge' => round($todayRecharge, 2),
            'todayWithdraw' => round($todayWithdraw, 2),
            'todaySendAmount' => round($todaySendAmount, 2),

        ];
        return ['state'=>1,'data'=>$return];
    }
}
