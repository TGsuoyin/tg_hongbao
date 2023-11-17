<?php

namespace App\Admin\Metrics\Hongbao;

use App\Models\LuckyHistory;
use App\Models\TgUser;
use Carbon\Carbon;
use Dcat\Admin\Widgets\Metrics\Line;
use Illuminate\Http\Request;

class TodayMineRate extends Line
{
    /**
     * @var string
     */
    protected $label = '今日中雷率';

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

        $arr = LuckyHistory::query()->selectRaw('count(*) as countThunder,is_thunder')->where('created_at','>',Carbon::now()->startOfDay())->where('created_at','<',Carbon::now()->endOfDay())->groupBy('is_thunder')->get();
        $is_thunder_0 = 0;
        $is_thunder_1 = 0;
        foreach ($arr as $val){
            if($val['is_thunder']==0){
                $is_thunder_0 = $val['countThunder'];
            }else{
                $is_thunder_1 = $val['countThunder'];
            }
        }
        $rate = 0;
        if($is_thunder_1>0||$is_thunder_0>0){
            $rate = round(($is_thunder_1/($is_thunder_1+$is_thunder_0)) * 100,2);
        }

        $this->withContent($rate.'%');
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
