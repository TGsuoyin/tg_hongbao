<?php

namespace App\Admin\Controllers;

use App\Admin\Metrics\Examples;
use App\Admin\Metrics\Hongbao\TodayCommission;
use App\Admin\Metrics\Hongbao\TodayLuckyMoney;
use App\Admin\Metrics\Hongbao\TodayLuckyMoneySum;
use App\Admin\Metrics\Hongbao\TodayMineRate;
use App\Admin\Metrics\Hongbao\TodayRecharge;
use App\Admin\Metrics\Hongbao\TodayReward;
use App\Admin\Metrics\Hongbao\TodayUser;
use App\Admin\Metrics\Hongbao\TodayWithdraw;
use App\Admin\Metrics\HongbaoTotal\RechargeSum;
use App\Admin\Metrics\HongbaoTotal\RewardSum;
use App\Admin\Metrics\HongbaoTotal\TotalCommission;
use App\Admin\Metrics\HongbaoTotal\TotalLuckyMoneyCount;
use App\Admin\Metrics\HongbaoTotal\TotalLuckyMoneySum;
use App\Admin\Metrics\HongbaoTotal\TotalUser;
use App\Admin\Metrics\HongbaoTotal\WithdrawSum;
use App\Http\Controllers\Controller;
use Dcat\Admin\Http\Controllers\Dashboard;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('主页')
            ->description('控制台')
            ->body(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(3, new TodayUser());
                        $row->column(3, new TodayLuckyMoney());
                        $row->column(3, new TodayLuckyMoneySum());
                        $row->column(3, new TodayMineRate());
                    });

                });
                $row->column(12, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(3, new TodayCommission());
                        $row->column(3, new TodayReward());
                        $row->column(3, new TodayRecharge());
                        $row->column(3, new TodayWithdraw());
                    });

                });
                $row->column(12, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(3, new TotalUser());
                        $row->column(3, new TotalLuckyMoneyCount());
                        $row->column(3, new TotalLuckyMoneySum());
                        $row->column(3, new RewardSum());
                    });

                });
                $row->column(12, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(3, new RechargeSum());
                        $row->column(3, new WithdrawSum());
                        $row->column(3, new TotalCommission());
                    });

                });
            });
    }
}
