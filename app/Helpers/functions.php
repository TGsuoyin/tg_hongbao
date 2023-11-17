<?php

use App\Models\AuthGroup;
use App\Services\ConfigService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;


function pp($arr)
{
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
    exit;
}


if (!function_exists('user_admin_config')) {
    function user_admin_config($key = null, $value = null)
    {
        $session = session();

        if (!$config = $session->get('admin.config')) {
            $config = config('admin');

            $config['lang'] = config('app.locale');
        }

        if (is_array($key)) {
            // 保存
            foreach ($key as $k => $v) {
                Arr::set($config, $k, $v);
            }

            $session->put('admin.config', $config);

            return;
        }

        if ($key === null) {
            return $config;
        }

        return Arr::get($config, $key, $value);
    }
}
/**
 * 红包雷算法
 *
 * @param float $totalAmount 红包总金额
 * @param int $totalCount 红包总个数
 * @param float $minAmount 每个红包最小金额
 * @param float $maxAmount 每个红包最大金额
 * @return array
 */
function red_envelope($totalAmount, $totalCount, $minAmount, $maxAmount, $thunder,$chat_id=0, $chance = 30): array
{
    $leftAmount = $totalAmount; // 剩余金额
    $leftCount = $totalCount; // 剩余个数
    $hasThunder = 0;
    if (mt_rand(1, 100) <= $chance) {
        $hasThunder = 1;
    }
    $straight_rate = ConfigService::getConfigValue($chat_id, 'straight_rate');
    $leopard_rate = ConfigService::getConfigValue($chat_id, 'leopard_rate');
    $noStraight = true;
    if (mt_rand(1, 100) <= $straight_rate) {
        $noStraight = false;
    }
    $noLeopard = true;
    if (mt_rand(1, 100) <= $leopard_rate) {
        $noLeopard = false;
    }
    while(true){
        if($hasThunder ==0 ){
            $result = get_nothunder_list($totalCount,$leftCount,$leftAmount,$maxAmount,$minAmount,$thunder,$noStraight,$noLeopard);
        }else{
            $result = get_thunder_list($leftCount,$leftAmount,$maxAmount,$minAmount,$thunder,$noStraight,$noLeopard);
        }
        if(count(array_unique($result)) == $totalCount){
            break;
        }
    }

    return $result;
}
function get_thunder_list($leftCount,$leftAmount,$maxAmount,$minAmount,$thunder,$noStraight,$noLeopard): array
{
    $thunderResult = [];
    $thunderCount = 1;
    if (mt_rand(1, 100) <= 30) {
        $thunderCount = 2;
    }
    if (mt_rand(1, 100) <= 10) {
        $thunderCount = 3;
    }
    if (mt_rand(1, 100) <= 5) {
        $thunderCount = 4;
    }

    for ($i = 1; $i <= $thunderCount; $i++) {
        $amount = get_thunder_num($maxAmount,$minAmount,$leftAmount,$leftCount,$thunder);
        if($noStraight && straight_check($amount)){
            while (true){
                $amount = get_thunder_num($maxAmount,$minAmount,$leftAmount,$leftCount,$thunder);
                if(!straight_check($amount)){
                    break;
                }
            }
        }
        if($noLeopard && leopard_check($amount)){
            while (true){
                $amount = get_thunder_num($maxAmount,$minAmount,$leftAmount,$leftCount,$thunder);
                if(!leopard_check($amount)){
                    break;
                }
            }
        }
        if($amount > 0 ){
            $thunderResult[] = $amount;
            if ($leftCount > 1) {
                $leftAmount -= $amount;
                $leftCount--;
            }
        }
    }
    $noResult = get_nothunder_list($leftCount,$leftCount,$leftAmount,$maxAmount,$minAmount,$thunder,$noStraight,$noLeopard);
    $newResult = array_merge($thunderResult,$noResult);
    shuffle($newResult);
    return $newResult;
}
function get_thunder_num($maxAmount,$minAmount,$leftAmount,$leftCount,$thunder){
    $max = min($maxAmount, $leftAmount - ($leftCount - 1) * $minAmount*2); // 红包最大金额不能超过剩余金额和最大金额的较小值
    $min = max($minAmount, $leftAmount - ($leftCount - 1) * $maxAmount); // 红包最小金额不能低于剩余金额和最小金额的较大值
    if($max < $min){
        $tmp = $max;
        $max = $min;
        $min = $tmp;
    }
    $amount = get_random_amount($min, $max, 1);
    $amount = number_format($amount, 1, '.', '') . $thunder;
    return $amount;
}
function get_nothunder_list($totalCount,$leftCount,$leftAmount,$maxAmount,$minAmount,$thunder,$noStraight,$noLeopard): array
{
    $result = [];
    for ($i = 1; $i <= $totalCount; $i++) {
        if ($leftCount == 1) {
                $amount = number_format($leftAmount, 2, '.', '');
                if (substr($amount, strlen($amount) - 1, 1) == $thunder) {
                    $dec = 1;
                    while (true) {
                        if ($leftAmount <= $dec / 100) {
                            if (count($result) <= 1) {
                                break;
                            }
                            $amount = number_format((float)$leftAmount + $dec / 100, 2, '.', '');
                            $j = 1;

                            while ($result[count($result) - $j] - $dec / 100 > 0 && $j <= count($result)) {
                                $lastKey = count($result) - $j;
                                $lastOne = number_format($result[$lastKey] - $dec / 100, 2, '.', '');
                                if (substr($amount, strlen($amount) - 1, 1) != $thunder && substr($lastOne, strlen($lastOne) - 1, 1) != $thunder) {
                                    $result[$lastKey] = $lastOne;
                                    break 2;
                                } else {
                                    $dec++;
                                }
                                $j++;
                            }
                        } else {
                            $amount = number_format($leftAmount - $dec / 100, 2, '.', '');
                            $lastOne = number_format($result[count($result) - 1] + $dec / 100, 2, '.', '');
                            if (substr($amount, strlen($amount) - 1, 1) != $thunder && substr($lastOne, strlen($lastOne) - 1, 1) != $thunder) {
                                $result[count($result) - 1] = $lastOne;
                                break;
                            } else {
                                $dec++;
                            }
                        }
                    }
                }

        }else{
            $tail = get_tail($thunder);
            $max = min($maxAmount, $leftAmount - ($leftCount - 1) * $minAmount*2); // 红包最大金额不能超过剩余金额和最大金额的较小值
            $min = max($minAmount, $leftAmount - ($leftCount - 1) * $maxAmount); // 红包最小金额不能低于剩余金额和最小金额的较大值
            if($max <$min){
                $tmp = $max;
                $max = $min;
                $min = $tmp;
            }
            $amount = get_random_amount($min, $max, 1);
            $amount = number_format($amount, 1, '.', '') . $tail;
            if($noStraight && straight_check($amount)){
                while (true){
                    $amount = get_random_amount($min, $max, 1);
                    $amount = number_format($amount, 1, '.', '') . $tail;
                    if(!straight_check($amount)){
                        break;
                    }
                }
            }
            if($noLeopard && leopard_check($amount)){
                while (true){
                    $amount = get_random_amount($min, $max, 1);
                    $amount = number_format($amount, 1, '.', '') . $tail;
                    if(!leopard_check($amount)){
                        break;
                    }
                }
            }
        }
        $result[] = abs($amount);
        if ($leftCount > 1) {
            $leftAmount -= $amount;
            $leftCount--;
        }
    }
    return $result;
}

function get_tail($thunder){
    $numbers = [0,1,2,3,4,5,6,7,8,9];
    $filteredNumbers = [];
    foreach ($numbers as $number) {
        if ($number != $thunder) {
            $filteredNumbers[] = $number;
        }
    }
    return $filteredNumbers[array_rand($filteredNumbers)];
}
function get_random_amount($minAmount, $maxAmount,$decimal=2){
    $amount = $minAmount;
    while (true){
        $amount = round(mt_rand($minAmount * 10, $maxAmount * 10) / 10, $decimal); // 保留两位小数
        if($amount>0){
            break;
        }
    }
    return $amount;
}
function red_envelope2($totalAmount, $totalCount, $minAmount, $maxAmount, $thunder, $chance = 30)
{
    $result = array();
    $leftAmount = $totalAmount; // 剩余金额
    $leftCount = $totalCount; // 剩余个数
//    $averageAmount = $totalAmount / $totalCount; // 平均金额

    $hasThunder = 0;
    if (mt_rand(1, 100) <= $chance) {
        $hasThunder = 1;
    }
    if ($hasThunder == 0) {
        for ($i = 1; $i <= $totalCount; $i++) {
            if ($leftCount == 1) {
                // 最后一个红包，剩余金额全部放入红包
                $amount = round($leftAmount, 2);
                if (substr($amount, strlen($amount) - 1, 1) == $thunder) {
                    $dec = 1;
                    while (true) {
                        $amount = round($leftAmount - $dec / 100, 2);
                        if (count($result) > 1) {
                            $lastOne = $result[count($result) - 1] + $dec / 100;
                            if (substr($amount, strlen($amount) - 1, 1) != $thunder && substr($lastOne, strlen($lastOne) - 1, 1) != $thunder) {
                                break;
                            } else {
                                $dec++;
                            }
                        } else {
                            break;
                        }

                    }
                }
            } else {
                // 计算随机金额
                $max = min($maxAmount, $leftAmount - ($leftCount - 1) * $minAmount); // 红包最大金额不能超过剩余金额和最大金额的较小值
                $min = max($minAmount, $leftAmount - ($leftCount - 1) * $maxAmount); // 红包最小金额不能低于剩余金额和最小金额的较大值

                while (true) {
                    $amount = round(mt_rand($min * 100, $max * 100) / 100, 2); // 保留两位小数
                    $amount = number_format($amount, 2, '.', '');
                    if (substr($amount, strlen($amount) - 1, 1) != $thunder) {
                        break;
                    }
                }
            }
            $result[] = number_format($amount, 2, '.', '');
            if ($leftCount > 1) {
                $leftAmount -= $amount;
                $leftCount--;
            }

        }
    } else {
        $twoThunder = 0;
        if (mt_rand(1, 100) <= 20) {
            $twoThunder = 1;
        }
        $thunderAmount2 = 0;
        if ($twoThunder == 0) {
            $thunderNum = 1;
            $thunderAmount = round(mt_rand($minAmount * 100, $maxAmount * 100) / 100, 2);
            $thunderAmount = number_format($thunderAmount, 2, '.', '');
            $thunderAmount = substr($thunderAmount, 0, strlen($thunderAmount) - 1) . $thunder;
            $leftAmount -= $thunderAmount;
            $leftCount--;
        } else {
            $thunderNum = 2;
            $thunderAmount = round(mt_rand($minAmount * 100, $maxAmount * 100) / 100, 2);
            $thunderAmount = number_format($thunderAmount, 2, '.', '');
            $thunderAmount = substr($thunderAmount, 0, strlen($thunderAmount) - 1) . $thunder;
            $leftAmount -= $thunderAmount;
            $leftCount--;
            $max2 = min($maxAmount, $leftAmount - ($leftCount - 1) * $minAmount); // 红包最大金额不能超过剩余金额和最大金额的较小值
            $min2 = max($minAmount, $leftAmount - ($leftCount - 1) * $maxAmount); // 红包最小金额不能低于剩余金额和最小金额的较大值
            $thunderAmount2 = round(mt_rand($min2 * 100, $max2 * 100) / 100, 2);
            $thunderAmount2 = substr($thunderAmount2, 0, strlen($thunderAmount2) - 1) . $thunder;
            $leftAmount -= $thunderAmount2;
            $leftCount--;
        }

        for ($i = 1; $i <= $totalCount - $thunderNum; $i++) {
            if ($leftCount == 1) {
                // 最后一个红包，剩余金额全部放入红包
                $amount = round($leftAmount, 2);
                if (substr($amount, strlen($amount) - 1, 1) == $thunder) {
                    $dec = 1;
                    while (true) {
                        $amount = round($leftAmount - $dec / 100, 2);
                        $lastOne = round($result[count($result) - 1] + $dec / 100, 2);
                        if (substr($amount, strlen($amount) - 1, 1) != $thunder && substr($lastOne, strlen($lastOne) - 1, 1) != $thunder) {
                            break;
                        } else {
                            $dec++;
                        }
                    }
                }
            } else {
                // 计算随机金额
                $max = min($maxAmount, $leftAmount - ($leftCount - 1) * $minAmount); // 红包最大金额不能超过剩余金额和最大金额的较小值
                $min = max($minAmount, $leftAmount - ($leftCount - 1) * $maxAmount); // 红包最小金额不能低于剩余金额和最小金额的较大值

                while (true) {
                    $amount = round(mt_rand($min * 100, $max * 100) / 100, 2); // 保留两位小数
                    $amount = number_format($amount, 2, '.', '');
                    if (substr($amount, strlen($amount) - 1, 1) != $thunder) {
                        break;
                    }
                }
            }
            $result[] = number_format($amount, 2, '.', '');
            if ($leftCount > 1) {
                $leftAmount -= $amount;
                $leftCount--;
            }
        }

        $thunderRandom = mt_rand(0, count($result));
        array_splice($result, $thunderRandom, 0, $thunderAmount);
        if ($thunderAmount2 > 0) {
            $thunderRandom = mt_rand(0, count($result));
            array_splice($result, $thunderRandom, 0, $thunderAmount2);
        }
    }
    return $result;
}

//豹子判断
function leopard_check($amount): bool
{
    $amount = number_format($amount, 2, '.', '');
    $number = str_replace('.', '', $amount);
    // 判断组成的每个数字是否相同
    $digits = str_split($number);
    if (count($digits) < 3) {
        return false;
    }
    $firstDigit = $digits[0];
    foreach ($digits as $digit) {
        if ($digit !== $firstDigit) {
            return false;
        }
    }

    return true;
}
function amount_count($amount)
{
    $amount = number_format($amount, 2, '.', '');
    $number = str_replace('.', '', $amount);
    return strlen($number) ;
}
//顺子判断
function straight_check($amount): bool
{
    if ($amount < 1) {
        return false;
    }
    $numberString = str_replace('.', '', $amount);
    if (strlen($numberString) < 3) {
        return false;
    }
    for ($i = 1; $i < strlen($numberString); $i++) {
        if ($numberString[$i] != ($numberString[$i - 1] + 1)) {
            return false;
        }
    }
    return true;
}

function check_thunder_num($list, $thunder)
{
    $thunderNum = 0;
    foreach ($list as $val) {
        $lastNum = substr($val, strlen($val) - 1, 1);
        if ($thunder == $lastNum) {
            $thunderNum++;
        }
    }
    return $thunderNum;
}

function format_name($str, $maxLen = 8)
{
    if (strlen($str) > $maxLen) {
        $str = mb_substr($str, 0, $maxLen - 3) . '..';
    }
    return $str;
}

function format_float($str)
{
    return str_replace(".", "\.", $str);
}

function add_log($msg, $type = '日志', $filename = 'local_log')
{
    $handler = new \Monolog\Handler\RotatingFileHandler(storage_path('logs/' . $filename . '.log'));
    $handler->setFormatter(new \App\Logging\LogJsonFormatter());
    (new \Monolog\Logger($type))
        ->pushHandler($handler)
        ->info($msg);
}

function common_reply_markup($chatId, $InlineKeyboardMarkup = null)
{
    $key = 'group_'.$chatId;
    $groupInfo = Redis::get($key);
    if(!$groupInfo){
        $groupInfo = AuthGroup::query()->where('status', 1)->where('group_id', $chatId)->first();
        if($groupInfo){
            $groupInfo = $groupInfo->toArray();
        }else{
            return [];
        }
        Redis::setex($key,60,serialize($groupInfo));
    }else{
        $groupInfo = unserialize($groupInfo);
    }

    if ($InlineKeyboardMarkup) {
        $markUps = $InlineKeyboardMarkup->addRow(
            InlineKeyboardButton::make(trans('telegram.btn_service'), url: $groupInfo['service_url']),
            InlineKeyboardButton::make(trans('telegram.btn_recharge'), url: $groupInfo['recharge_url']),
            InlineKeyboardButton::make(trans('telegram.btn_rule'), url: $groupInfo['channel_url']),
            InlineKeyboardButton::make(trans('telegram.btn_balance'), callback_data: 'balance'),
        )->addRow(
            InlineKeyboardButton::make(trans('telegram.btn_invitelink'), callback_data: "invitelink"),
            InlineKeyboardButton::make(trans('telegram.btn_promotion'), callback_data: 'share_data'),
        )->addRow(
            InlineKeyboardButton::make(trans('telegram.btn_report'), callback_data: "today_data"),
            InlineKeyboardButton::make(trans('telegram.yesterday_report'), callback_data: 'yesterday_data'),
            InlineKeyboardButton::make(trans('telegram.team_report'), callback_data: 'team_report'),
        );
    } else {
        $markUps =  InlineKeyboardMarkup::make()->addRow(
            InlineKeyboardButton::make(trans('telegram.btn_service'), url: $groupInfo['service_url']),
            InlineKeyboardButton::make(trans('telegram.btn_recharge'), url: $groupInfo['recharge_url']),
            InlineKeyboardButton::make(trans('telegram.btn_rule'), url: $groupInfo['channel_url']),
            InlineKeyboardButton::make(trans('telegram.btn_balance'), callback_data: 'balance'),
        )->addRow(
            InlineKeyboardButton::make(trans('telegram.btn_invitelink'), callback_data: "invitelink"),
            InlineKeyboardButton::make(trans('telegram.btn_promotion'), callback_data: 'share_data'),
        )->addRow(
            InlineKeyboardButton::make(trans('telegram.btn_report'), callback_data: "today_data"),
            InlineKeyboardButton::make(trans('telegram.yesterday_report'), callback_data: 'yesterday_data'),
            InlineKeyboardButton::make(trans('telegram.team_report'), callback_data: 'team_report'),
        );
    }
    $common_reply_markup = config('reply_markup.common_reply_markup');
    if($common_reply_markup){
        foreach ($common_reply_markup as $line){
            $buttons = [];
            foreach ($line as $row){
                $buttons[] = InlineKeyboardButton::make($row['text'], $row['url'],null,$row['callback_data']);
            }
            $markUps->addRow(...$buttons);
        }
    }


    return $markUps;


}
function del_lucklist($luckyId){
    $luckyKey = 'lucky_' . $luckyId;
    Redis::del($luckyKey);
    $historyListKey = 'history_list_' . $luckyId;
    Redis::del($historyListKey);

}

function get_photo($groupId)
{
    $key = 'photo_'.$groupId;
    $photoId = Redis::get($key);
    if(!$photoId){
        $groupInfo = AuthGroup::query()->where('status', 1)->where('group_id', $groupId)->first();
        $photoId = $groupInfo['photo_id'];
        Redis::setex($key,60,$photoId);
    }

    return $photoId;
}
function money_log($groupId,$tgId,$amount,$type,$remark='',$lucky_id=null){
    if($amount == 0 ){
        return false;
    }
    $balance = \App\Models\TgUser::query()->where('group_id',$groupId)->where('tg_id',$tgId)->value('balance');
    $insert = [
        'amount' => $amount,
        'tg_id' => $tgId,
        'group_id' => $groupId,
        'type' => $type,
        'remark' => $remark,
        'lucky_id' => $lucky_id,
        'balance' => $balance,
    ];
    return \App\Models\MoneyLog::query()->create($insert);
}
