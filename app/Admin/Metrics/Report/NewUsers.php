<?php

namespace App\Admin\Metrics\Report;

use App\Models\TgUser;
use Dcat\Admin\Widgets\Metrics\Line;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NewUsers extends Line
{
    protected $label = '新增用户';
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
            '7' => '最近7天',
            '15' => '最近15天',
            '30' => '最近一个月',
            '180' => '最近半年',
            '365' => '最近一年',
        ]);
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
            default:
                $start_date=Carbon::now()->subDays(7);
                $end_date = Carbon::now();

        }
        $array = $this->getUserByDate($start_date,$end_date);
        $array = array_column($array,'value');
        $count = $this->getUserCount($start_date,$end_date);
        // 卡片内容
        $this->withContent($count);
        // 图表数据
        $this->withChart($array);
    }
    public function getUserByDate($start_date,$end_date){
        return TgUser::query()->where('created_at','>',Carbon::parse($start_date))
            ->where('created_at','<',Carbon::parse($end_date))
            ->groupBy('date')
            ->get([DB::raw('DATE(created_at) as date'),DB::raw('COUNT(*) as value')])
            ->toArray();
    }
    public function getUserCount($start_date,$end_date){
        return TgUser::query()->where('created_at','>',Carbon::parse($start_date))
            ->where('created_at','<',Carbon::parse($end_date))
            ->count();
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
