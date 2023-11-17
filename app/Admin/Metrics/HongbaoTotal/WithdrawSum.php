<?php

namespace App\Admin\Metrics\HongbaoTotal;

use App\Models\LuckyMoney;
use App\Models\RechargeRecord;
use App\Models\RewardRecord;
use App\Models\TgUser;
use App\Models\WithdrawRecord;
use Carbon\Carbon;
use Dcat\Admin\Widgets\Metrics\Line;
use Illuminate\Http\Request;

class WithdrawSum extends Line
{
    /**
     * @var string
     */
    protected $label = '提现汇总';

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
        $todaySum = WithdrawRecord::query()->sum('amount');
        $this->withContent($todaySum.'U');
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
