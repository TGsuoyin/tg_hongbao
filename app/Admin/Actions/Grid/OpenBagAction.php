<?php

namespace App\Admin\Actions\Grid;

use App\Jobs\LuckyHistoryJob;
use App\Models\LuckyMoney;
use App\Services\LuckyMoneyService;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Models\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

class OpenBagAction extends RowAction
{
    /**
     * @return string
     */
	protected $title = '<i class="fa fa-hand-pointer-o"></i> 手动开包';

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        // dump($this->key());
        $bot = new Nutgram(config('nutgram.token'),[
            'api_url'=>env('BASE_BOT_URL'),
            'timeout' => 86400
        ]);
        $id = $this->getKey();
        $luckyInfo = LuckyMoney::query()->where('id',$id)->first();
        $list = LuckyMoneyService::getLuckyHistory($id);
        if($luckyInfo['status'] == 3){
            $details = '';
            $loseMoneyTotal = 0;
            $qiangTotal = 0;
            foreach ($list as $key => $val) {
                $qiangTotal += $val['amount'];
                if ($val['is_thunder'] != 1) {
                    $details .= ($key + 1) . ".[💵] <code>" . number_format(round($val['amount'], 2), 2, '.', '') . "</code> U <code>" . format_name($val['first_name']) . "</code>\n";
                } else {
                    $details .= ($key + 1) . ".[💣] <code>" . number_format(round($val['amount'], 2), 2, '.', '') . "</code> U <code>" . format_name($val['first_name']) . "</code>\n";
                    $loseMoneyTotal += $val['lose_money'];
                }
            }
            $shengyu = round($luckyInfo['amount'] - $qiangTotal, 2);
            $shengyuText = $shengyu > 0 ? '(已退回)' : '';
            $profit = round($loseMoneyTotal + $shengyu - $luckyInfo['amount'], 2);
            $profitTxt = $profit >= 0 ? '+' . $profit : $profit;
            if ($luckyInfo['type'] == 1) {


                $caption = "[ <code>" . format_name($luckyInfo['sender_name']) . "</code> ]的红包已过期！\n
🧧红包金额：" . (int)$luckyInfo['amount'] . " U
🛎红包倍数：" . round($luckyInfo['lose_rate'], 2) . "
💥中雷数字：{$luckyInfo['thunder']}\n
--------领取详情--------\n
" . $details . "
<pre>💹 中雷盈利： " . $loseMoneyTotal . "</pre>
<pre>💹 发包成本：-" . round($luckyInfo['amount'], 2) . "</pre>
<pre>💹 已领取：" . round($qiangTotal, 2) . "</pre>
<pre>💹 剩余：" . round($shengyu, 2) . $shengyuText . "</pre>
<pre>💹 包主实收：{$profitTxt}</pre>
温馨提示：[ <code>" . format_name($luckyInfo['sender_name']) . "</code> ]的红包已过期！";

            } else {

                $caption = "[ <code>" . format_name($luckyInfo['sender_name']) . "</code> ]的福利红包已过期！\n
🧧红包金额：" . $luckyInfo['amount'] . " U

--------领取详情--------\n
" . $details . "
<pre>💹 发包成本：-" . round($luckyInfo['amount'], 2) . "</pre>
<pre>💹 已领取：" . round($qiangTotal, 2) . "</pre>
<pre>💹 剩余：" . round($shengyu, 2) . $shengyuText . "</pre>
温馨提示：[ <code>" . format_name($luckyInfo['sender_name']) . "</code> ]的福利红包已过期！";

            }

            $data = [
                'message_id' => $luckyInfo['message_id'],
                'chat_id' => $luckyInfo['chat_id'],
                'caption' => $caption,
                'parse_mode' => ParseMode::HTML,
                'reply_markup' => common_reply_markup($luckyInfo['chat_id'])
            ];
            try {
                $rs = $bot->editMessageCaption($data);
                if (!$rs) {
                    return $this->response()->error('信息编辑失败');
                }
                return $this->response()->success('成功！');
            } catch (\Exception $e) {
                if ($e->getCode() == 429) {
                    $errMsg = '请求太频繁';
                } else {
                    $errMsg = $e->getMessage();
                }
                if(strpos('exactly the same as a current content',$errMsg)!==false){
                    $errMsg = "【包已打开】指定的新消息内容和回复标记与消息的当前内容和回复标签完全相同";
                }
                return $this->response()->error($errMsg);
            }
        }else{

            $details = '';
            $loseMoneyTotal = 0;
            foreach ($list as $key => $val) {
                if ($val['is_thunder'] != 1) {
                    $details .= ($key + 1) . ".[💵] <code>" . number_format(round($val['amount'], 2), 2, '.', '') . "</code> U <code>" . format_name($val['first_name']) . "</code>\n";
                } else {
                    $details .= ($key + 1) . ".[💣] <code>" . number_format(round($val['amount'], 2), 2, '.', '') . "</code> U <code>" . format_name($val['first_name']) . "</code>\n";
                    $loseMoneyTotal += $val['lose_money'];
                }
            }
            $luckyAmount = (int)$luckyInfo['amount'];
            $profit = $loseMoneyTotal - $luckyInfo['amount'];
            $profitTxt = $profit >= 0 ? '+' . $profit : $profit;


            if ($luckyInfo['type'] == 1) {
                $caption = "[ <code>" . format_name($luckyInfo['sender_name']) . "</code> ]的红包已被领完！\n
🧧红包金额：" . $luckyAmount . " U
🛎红包倍数：" . round($luckyInfo['lose_rate'], 2) . "
💥中雷数字：{$luckyInfo['thunder']}\n
--------领取详情--------\n
" . $details . "
<pre>💹 中雷盈利： " . round($loseMoneyTotal, 2) . "</pre>
<pre>💹 发包成本：-" . $luckyAmount . "</pre>
<pre>💹 包主实收：{$profitTxt}</pre>";
            } else {
                $caption = "[ <code>" . format_name($luckyInfo['sender_name']) . "</code> ]的福利红包已被领完！\n
🧧红包金额：" . $luckyInfo['amount'] . " U
\n
--------领取详情--------\n
" . $details . "
<pre>💹 发包成本：-" . $luckyAmount . "</pre>
";
            }
            $data = [
                'message_id' => $luckyInfo['message_id'],
                'caption' => $caption,
                'parse_mode' => ParseMode::HTML,
                'reply_markup' => common_reply_markup($luckyInfo['chat_id']),
                'chat_id' => $luckyInfo['chat_id']
            ];
            $errMsg = '';
            try {
                $bot->editMessageCaption($data);
                if($luckyInfo['status'] == 1 && $luckyInfo['number'] <= $luckyInfo['received_num']){
                    $luckyInfo->status = 2;
                    $luckyInfo->save();
                }

            } catch (\Exception $e) {
                Log::error('抢包完成修改消息异常=>' . $e->getCode() . '  msg=>' . $e->getMessage().' line=>'.$e->getLine());
                if ($e->getCode() == 429) {
                    $errMsg = '请求太频繁';
                } else {
                    $errMsg = $e->getMessage();
                }
            }
            if(!$errMsg){
                return $this->response()->success('成功！');
            }else{
                $errMsg = '出错了！'.$errMsg;
                if(strpos('exactly the same as a current content',$errMsg)!==false){
                    $errMsg = "【包已打开】指定的新消息内容和回复标记与消息的当前内容和回复标签完全相同";
                }
                return $this->response()->error($errMsg);
            }
        }


    }

    /**
     * @return string|void
     */
    public function confirm()
    {
        $id = $this->getKey();
        $luckyInfo = LuckyMoney::query()->where('id',$id)->first();

        if($luckyInfo['number'] > $luckyInfo['received_num'] && $luckyInfo['status'] == 1){
            return '红包未领完，确定直接开包？';
        }else{
            return '你确定要手动开包？';
        }

    }

    /**
     * @param Model|Authenticatable|HasPermissions|null $user
     *
     * @return bool
     */
    protected function authorize($user): bool
    {
        return $user->can('openbag');
    }

    /**
     * @return array
     */
    protected function parameters()
    {
        return [];
    }
}
