<?php

namespace App\Admin\Metrics\Hongbao;

use App\Models\CommissionRecord;
use App\Models\RewardRecord;
use App\Models\TgUser;
use Carbon\Carbon;
use Dcat\Admin\Widgets\Metrics\Line;
use Illuminate\Http\Request;

class TodayCommission extends Line
{
    /**
     * @var string
     */
    protected $label = '今日盈利';

    /**
     * 初始化卡片内容
     *
     * @return void
     */
    protected function init()
    {
        parent::init();

        $this->title($this->label);

    }

    /**
     * 处理请求
     *
     * @param Request $request
     *
     * @return mixed|void
     */
    public function handle(Request $request)
    {

        $todayCommission = CommissionRecord::query()->where('created_at','>',Carbon::now()->startOfDay())->where('created_at','<',Carbon::now()->endOfDay())->sum('amount');
        $todayReward = RewardRecord::query()->where('created_at','>',Carbon::now()->startOfDay())->where('created_at','<',Carbon::now()->endOfDay())->sum('amount');
        $dif = $todayCommission - $todayReward;
        $text = $todayCommission .' - '.$todayReward.' = '.$dif;
        $this->withContent($text);
    }


    /**
     * 设置卡片内容.
     *
     * @param string $content
     *
     * @return $this
     */
    public function withContent($content)
    {
        return $this->content(
            <<<HTML
<div class="d-flex justify-content-between align-items-center mt-1" style="margin-bottom: 2px">
    <h2 class="ml-1 font-lg-1">{$content}</h2>
</div>
HTML
        );
    }
}
