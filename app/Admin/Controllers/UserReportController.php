<?php

namespace App\Admin\Controllers;

use App\Admin\Metrics\Examples\NewDevices;
use App\Admin\Metrics\Examples\TotalUsers;
use App\Admin\Metrics\Report\NewUsers;
use App\Admin\Renderable\AuthGroupTable;
use App\Admin\Renderable\UserTable;
use App\Admin\Repositories\CommissionReport;
use App\Admin\Repositories\LuckyReport;
use App\Admin\Repositories\Report;
use App\Admin\Repositories\RewardReport;
use App\Admin\Repositories\UserReport;
use App\Http\Controllers\Controller;
use App\Models\AuthGroup;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;

class UserReportController extends Controller
{
    use PreviewCode;

    public function index(Content $content)
    {
        return $content
            ->header('报表')
            ->description('')
            ->body(function ($row) {
                $row->column(3, $this->grid());
                $row->column(3, $this->gridLucky());
                $row->column(3, $this->gridcommission());
                $row->column(3, $this->gridReward());
            });
    }

    protected function grid()
    {
        return new Grid(new UserReport(), function (Grid $grid) {

            $grid->column('date','日期')->sortable();
            $grid->column('value','注册人数');

            // 开启responsive插件
            $grid;
            $grid->disableActions();
            $grid->disableBatchDelete();
            $grid->disableCreateButton();
            $grid->disableCreateButton();


            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->expand();
                $groupList = AuthGroup::query()->get();
                $selectData= [];
                foreach($groupList as $g){
                    $selectData[$g['group_id']] = $g['group_id']."({$g['remark']}))";
                }
                $filter->equal('group_id', '群ID')->select($selectData)->width('250px');
                $filter->equal('start_date', '开始日期')->date()->width('220px');
                $filter->equal('end_date', '结束日期')->date()->width('220px');
            });

        });
    }

    protected function gridLucky()
    {
        return new Grid(new LuckyReport(), function (Grid $grid) {

            $grid->column('date','日期')->sortable();
            $grid->column('value','发包数量');

            // 开启responsive插件
            $grid;
            $grid->disableActions();
            $grid->disableBatchDelete();
            $grid->disableCreateButton();
            $grid->disableCreateButton();


            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->expand();
                $groupList = AuthGroup::query()->get();
                $selectData= [];
                foreach($groupList as $g){
                    $selectData[$g['group_id']] = $g['group_id']."({$g['remark']}))";
                }
                $filter->equal('group_id', '群ID')->select($selectData)->width('250px');

                $filter->equal('start_date', '开始日期')->date()->width('220px');
                $filter->equal('end_date', '结束日期')->date()->width('220px');
            });
        });
    }
    protected function gridcommission()
    {
        return new Grid(new CommissionReport(), function (Grid $grid) {

            $grid->column('date','日期')->sortable();
            $grid->column('value','平台抽成金额');

            // 开启responsive插件
            $grid;
            $grid->disableActions();
            $grid->disableBatchDelete();
            $grid->disableCreateButton();
            $grid->disableCreateButton();


            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->expand();
                $groupList = AuthGroup::query()->get();
                $selectData= [];
                foreach($groupList as $g){
                    $selectData[$g['group_id']] = $g['group_id']."({$g['remark']}))";
                }
                $filter->equal('group_id', '群ID')->select($selectData)->width('250px');

                $filter->equal('start_date', '开始日期')->date()->width('220px');
                $filter->equal('end_date', '结束日期')->date()->width('220px');
            });
        });
    }protected function gridReward()
    {
        return new Grid(new RewardReport(), function (Grid $grid) {

            $grid->column('date','日期')->sortable();
            $grid->column('value','奖励金额');

            // 开启responsive插件
            $grid;
            $grid->disableActions();
            $grid->disableBatchDelete();
            $grid->disableCreateButton();
            $grid->disableCreateButton();


            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->expand();
                $groupList = AuthGroup::query()->get();
                $selectData= [];
                foreach($groupList as $g){
                    $selectData[$g['group_id']] = $g['group_id']."({$g['remark']}))";
                }
                $filter->equal('group_id', '群ID')->select($selectData)->width('250px');

                $filter->equal('start_date', '开始日期')->date()->width('220px');
                $filter->equal('end_date', '结束日期')->date()->width('220px');
            });
        });
    }
}
