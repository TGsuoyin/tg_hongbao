<?php

namespace App\Admin\Repositories;

use App\Models\TgUser;
use Dcat\Admin\Grid;
use Dcat\Admin\Repositories\Repository;
use Faker\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RewardReport extends Repository
{
    public function get(Grid\Model $model)
    {
        $start_date = $model->filter()->input('start_date');
        $end_date = $model->filter()->input('end_date');
        $group_id = $model->filter()->input('group_id');

        if ($start_date == '') {
            $start_date = Carbon::now()->subDays(7);
        }
        if ($end_date == '') {
            $end_date = Carbon::now();
        }

        $items = $this->getUserByDate($start_date, $end_date,$group_id);
        $total = [
            'date'=>'总数',
            'value'=>array_sum(array_column($items,'value')),
        ];
        $items[] = $total;
        return $model->makePaginator(
            count($items),
            $items
        );
    }

    public function getUserByDate($start_date, $end_date,$group_id = '')
    {
        $obj = \App\Models\RewardRecord::query()->where('created_at', '>', Carbon::parse($start_date))
            ->where('created_at', '<', Carbon::parse($end_date))
            ->groupBy('date')
            ->orderBy('date','desc');
        if($group_id){
            $obj = $obj->where('group_id',$group_id);
        }
            $obj = $obj->get([DB::raw('DATE(created_at) as date'), DB::raw('sum(amount) as value')])
            ->toArray();
            return  $obj;
    }

}
