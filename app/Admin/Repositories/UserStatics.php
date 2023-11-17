<?php

namespace App\Admin\Repositories;

use App\Models\TgUser;
use Dcat\Admin\Grid;
use Dcat\Admin\Repositories\Repository;
use Faker\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserStatics extends Repository
{
    public function get(Grid\Model $model)
    {
        $start_date = $model->filter()->input('start_date');
        $end_date = $model->filter()->input('end_date');
        $group_id = $model->filter()->input('group_id');
        $invite_user = $model->filter()->input('invite_user');
        $per_page = $model->filter()->input('per_page');
        $tg_id = $model->filter()->input('tg_id');

        if ($start_date == '') {
            $start_date = Carbon::now()->subDays(7);
        }
        if ($end_date == '') {
            $end_date = Carbon::now();
        }

        $result = $this->getUserByDate($start_date, $end_date,$group_id,$per_page,$invite_user,$tg_id);
        $items = $result['data'];
        $total = [
            'group_id'=>'总数',
            'tg_id'=>'',
            'name'=>'',
            'luckyCount'=>array_sum(array_column($items,'luckyCount')),
            'luckySum'=>array_sum(array_column($items,'luckySum')),
            'qiangAmount'=>array_sum(array_column($items,'qiangAmount')),
            'loseAmount'=>array_sum(array_column($items,'loseAmount')),
            'rechargeAmount'=>array_sum(array_column($items,'rechargeAmount')),
            'withdrawAmount'=>array_sum(array_column($items,'withdrawAmount')),
        ];
        $result['data'][] = $total;
        return $model->makePaginator(
            $result['total'],
            $result['data']
        );
    }

    public function getUserByDate($start_date, $end_date,$group_id = '',$per_page=20,$invite_user='',$tg_id='')
    {
        $userList = TgUser::query()->where(function ($query)use($group_id,$invite_user,$tg_id){
            if($invite_user){
                $query->where('invite_user', $invite_user);
            }
            if($tg_id){
                $query->where('tg_id', $tg_id);
            }
            if($group_id){
                $query->where('group_id', $group_id);
            }
        })->orderBy('id','desc')->paginate($per_page);
        $userList = $userList->toArray();

        if($group_id != ''){
            foreach ($userList['data'] as &$item){
                $new['group_id'] = $item['group_id'];
                $new['tg_id'] = $item['tg_id'];
                $new['name'] = $item['first_name']?$item['first_name']:$item['username'];
                $new['luckyCount'] = \App\Models\LuckyMoney::query()->where('chat_id',$group_id)->where('created_at', '>', Carbon::parse($start_date))->where('created_at', '<', Carbon::parse($end_date))->where('sender_id',$item['tg_id'])->count();
                $new['luckySum'] = \App\Models\LuckyMoney::query()->where('chat_id',$group_id)->where('created_at', '>', Carbon::parse($start_date))->where('created_at', '<', Carbon::parse($end_date))->where('sender_id',$item['tg_id'])->sum('amount');
                $new['qiangAmount'] = \App\Models\LuckyHistory::query()->leftJoin('lucky_money','lucky_money.id','=','lucky_history.lucky_id')->where('lucky_money.chat_id',$group_id)->where('lucky_money.created_at', '>', Carbon::parse($start_date))->where('lucky_money.created_at', '<', Carbon::parse($end_date))->where('lucky_history.user_id',$item['tg_id'])->sum('lucky_history.amount');
                $loseAmount= \App\Models\LuckyHistory::query()->leftJoin('lucky_money','lucky_money.id','=','lucky_history.lucky_id')->where('lucky_money.chat_id',$group_id)->where('lucky_money.created_at', '>', Carbon::parse($start_date))->where('lucky_money.created_at', '<', Carbon::parse($end_date))->where('lucky_history.user_id',$item['tg_id'])->where('lucky_history.is_thunder',1)->sum('lucky_history.lose_money');
                $new['loseAmount'] = $loseAmount>0?'-'.$loseAmount:0;
                $new['rechargeAmount'] = \App\Models\RechargeRecord::query()->where('group_id',$group_id)->where('created_at', '>', Carbon::parse($start_date))->where('created_at', '<', Carbon::parse($end_date))->where('tg_id',$item['tg_id'])->sum('amount');
                $withdrawAmount = \App\Models\WithdrawRecord::query()->where('group_id',$group_id)->where('created_at', '>', Carbon::parse($start_date))->where('created_at', '<', Carbon::parse($end_date))->where('tg_id',$item['tg_id'])->sum('amount');
                $new['withdrawAmount'] = $withdrawAmount>0?'-'.$withdrawAmount:0;
                $item = $new;
            }

            return  $userList;
        }else{
            foreach ($userList['data'] as &$item){
                $new['group_id'] = $item['group_id'];
                $new['tg_id'] = $item['tg_id'];
                $new['name'] = $item['first_name']?$item['first_name']:$item['username'];
                $new['luckyCount'] = \App\Models\LuckyMoney::query()->where('created_at', '>', Carbon::parse($start_date))->where('created_at', '<', Carbon::parse($end_date))->where('sender_id',$item['tg_id'])->count();
                $new['luckySum'] = \App\Models\LuckyMoney::query()->where('created_at', '>', Carbon::parse($start_date))->where('created_at', '<', Carbon::parse($end_date))->where('sender_id',$item['tg_id'])->sum('amount');
                $new['qiangAmount'] = \App\Models\LuckyHistory::query()->where('created_at', '>', Carbon::parse($start_date))->where('created_at', '<', Carbon::parse($end_date))->where('user_id',$item['tg_id'])->sum('amount');
                $new['loseAmount'] = \App\Models\LuckyHistory::query()->where('created_at', '>', Carbon::parse($start_date))->where('created_at', '<', Carbon::parse($end_date))->where('user_id',$item['tg_id'])->where('is_thunder',1)->sum('lose_money');
                $new['rechargeAmount'] = \App\Models\RechargeRecord::query()->where('created_at', '>', Carbon::parse($start_date))->where('created_at', '<', Carbon::parse($end_date))->where('tg_id',$item['tg_id'])->sum('amount');
                $new['withdrawAmount'] = \App\Models\WithdrawRecord::query()->where('created_at', '>', Carbon::parse($start_date))->where('created_at', '<', Carbon::parse($end_date))->where('tg_id',$item['tg_id'])->sum('amount');
                $item = $new;
            }

            return  $userList;
        }

    }

}
