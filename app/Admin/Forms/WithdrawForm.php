<?php

namespace App\Admin\Forms;

use App\Models\RechargeRecord;
use App\Models\TgUser;
use App\Models\WithdrawRecord;
use Dcat\Admin\Admin;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Dcat\Admin\Contracts\LazyRenderable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;

class WithdrawForm extends Form implements LazyRenderable
{
    use LazyWidget;

    // 使用异步加载功能
    protected function authorize($user): bool
    {
        return $user->can('user_withdraw');
    }

    public function failedAuthorization()
    {
        return $this->response()->error(__('admin.deny'));
    }

    // 处理请求
    public function handle(array $input)
    {
        // 获取外部传递参数
        $id = $this->payload['id'] ?? null;
//        $id = Helper::array($input['id'] ?? null);

        // 表单参数
        $amount = $input['amount'] ?? 0;
        $status = $input['status'] ?? 0;
        $addr = $input['addr'] ?? null;
        $addr_type = $input['addr_type'] ?? null;
        $remark = $input['remark'] ?? null;
        $sendChat = $input['sendChat'] ?? 0;
        if (!$id) {
            return $this->response()->error('参数错误');
        }
        if ($amount <= 0 || !is_numeric($amount)) {
            return $this->response()->error('数量必须大于0');
        }

        $user = TgUser::query()->find($id);

        if (!$user) {
            return $this->response()->error('用户不存在');
        }
        if ($user['balance'] < $amount) {
            return $this->response()->error('用户余额不足');
        }
        DB::beginTransaction();
        if($status == 0){
            return $this->response()->error('请给客户转账后再添加提现记录');
        }
        $balance = $user['balance'] - $amount;
        $rs = TgUser::query()->where('tg_id', $user->tg_id)->where('group_id', $user->group_id)->update(['balance' => $balance,'withdraw_addr'=>$addr]);
        if (!$rs) {
            DB::rollBack();
            return $this->response()->error('提现失败');
        }
        money_log($user->group_id,$user->tg_id,-$amount,'withdraw','提现');
        $insert = [
            'tg_id' => $user['tg_id'],
            'username' => $user['username'],
            'first_name' => $user['first_name'],
            'group_id' => $user['group_id'],
            'amount' => $amount,
            'remark' => $remark,
            'status' => $status,
            'address' => $addr,
            'addr_type' => $addr_type,
            'admin_id' => Admin::user()->id,
        ];
        $rs2 = WithdrawRecord::query()->create($insert);
        if (!$rs2) {
            DB::rollBack();
            return $this->response()->error('提现失败');
        }
        DB::commit();
        if($sendChat){
            $bot = new Nutgram(config('nutgram.token'),[
                'api_url'=>env('BASE_BOT_URL'),
                'timeout' => 86400
            ]);
            $bot->sendMessage('[ '.$user['first_name'].' ] 提现 '.$amount.' U',['chat_id'=>$user['group_id']]);
        }
        return $this->response()->success('提现成功')->refresh();
    }

    public function form()
    {
        // 获取外部传递参数
        $id = $this->payload['id'] ?? null;

        $this->number('amount', '提现数量')->required();
        $this->text('addr', '提现地址')->required();
        $this->select('addr_type', '提现类型')->options(['trc20'=>'trc20', 'bep20'=>'bep20'])->default('trc20')->required();
        $this->radio('status', '是否转账')->options(['未转账', '已转账'])->default('1')->required();
        $this->text('remark', '备注');
        $this->radio('sendChat','是否发送到群聊')->options(['否', '是'])->default('0');

        $this->hidden('id')->attribute('id', $id);
    }

}
