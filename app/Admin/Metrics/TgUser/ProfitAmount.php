<?php

namespace App\Admin\Metrics\TgUser;

use App\Models\LuckyMoney;
use App\Models\MoneyLog;
use App\Models\TgUser;
use Dcat\Admin\Widgets\Metrics\Line;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfitAmount extends Line
{
    protected $label = '个人盈利汇总';
    // 保存自定义参数
    protected $data = [];
    public function __construct(array $data = [])
    {
        $this->data = $data;

        parent::__construct();
    }
    /**
     * 初始化卡片内容
     *
     * @return void
     */
    protected function init()
    {
        parent::init();

        $this->title($this->label);
        $this->dropdown([
            '1' => '今日',
            '7' => '最近7天',
            '15' => '最近15天',
            '30' => '最近一个月',
            '180' => '最近半年',
            '365' => '最近一年',
        ]);
    }
    public function parameters() : array
    {
        return $this->data;
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


        switch ($request->get('option')) {
            case '365':
                $start_date=Carbon::now()->subDays(365);
                $end_date = Carbon::now();
                // 直线
                break;
            case '180':
                $start_date=Carbon::now()->subDays(180);
                $end_date = Carbon::now();
                // 直线
                break;
            case '30':
                $start_date=Carbon::now()->subDays(30);
                $end_date = Carbon::now();
                // 直线
                break;
            case '15':

                $start_date=Carbon::now()->subDays(15);
                $end_date = Carbon::now();
                // 直线
                break;
            case '7':
                $start_date=Carbon::now()->subDays(7);
                $end_date = Carbon::now();
                break;
            case '1':
            default:
                $start_date=Carbon::now()->startOfDay();
                $end_date = Carbon::now();

        }
        $tgId = $request->get('tg_id') ?? null;
        $array = $this->getByDate($start_date,$end_date,$tgId);
        $array = array_column($array,'value');
        $count = $this->getCount($start_date,$end_date,$tgId);
        // 卡片内容
        $this->withContent($count);
        // 图表数据
        $this->withChart($array);
    }
    public function getByDate($start_date,$end_date,$tgId){
        return MoneyLog::query()->where('created_at','>',Carbon::parse($start_date))
            ->where('created_at','<',Carbon::parse($end_date))
            ->where('tg_id',$tgId)
            ->groupBy('date')
            ->get([DB::raw('DATE(created_at) as date'),DB::raw('SUM(amount) as value')])
            ->toArray();
    }
    public function getCount($start_date,$end_date,$tgId){
        return MoneyLog::query()->where('created_at','>',Carbon::parse($start_date))
            ->where('created_at','<',Carbon::parse($end_date))
            ->where('tg_id',$tgId)
            ->sum('amount');
    }
    /**
     * 设置图表数据.
     *
     * @param array $data
     *
     * @return $this
     */
    public function withChart(array $data)
    {
        return $this->chart([
            'series' => [
                [
                    'name' => $this->label,
                    'data' => $data,
                ],
            ],

        ])->chartStraight();
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
    <span class="mb-0 mr-1 text-80">{$this->label}</span>
</div>
HTML
        );
    }
}
