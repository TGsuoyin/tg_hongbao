<?php

namespace App\Admin\Controllers;


use App\Admin\Actions\Grid\OpenBagAction;
use App\Admin\Repositories\UserStatics;
use App\Http\Controllers\Controller;
use App\Models\AuthGroup;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;

class UserStaticsController extends Controller
{
    use PreviewCode;

    public function index(Content $content)
    {
        return $content
            ->header('用户统计')
            ->description('')
            ->body(function ($row) {
                $row->column(12, $this->grid());
            });
    }

    protected function grid()
    {
        return Grid::make(new UserStatics(), function (Grid $grid) {

            $grid->column('group_id','群组ID');
            $grid->column('tg_id','tgID');
            $grid->column('name','用户名');
            $grid->column('luckyCount','发包数');
            $grid->column('luckySum','发包总金额');
            $grid->column('qiangAmount','抢包盈利');
            $grid->column('loseAmount','中雷金额');
            $grid->column('rechargeAmount','充值金额');
            $grid->column('withdrawAmount','提现金额');

            // 开启responsive插件
            $grid;
            $grid->disableBatchDelete();
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
                $filter->equal('invite_user', '邀请人ID')->width('250px');
                $filter->equal('tg_id', 'tgID')->width('250px');
                $filter->equal('group_id', '群ID')->select($selectData)->width('250px');
                $filter->equal('start_date', '开始日期')->date()->width('220px');
                $filter->equal('end_date', '结束日期')->date()->width('220px');
            });
            $grid->actions(function (\App\Admin\Actions\TextActions $actions) {
                $actions->disableView();
                $actions->disableDelete();
                $actions->disableEdit();

                $id = $actions->row->tg_id;
                $actionStr = " <a href=\"/admin/user-statics?invite_user={$id}\"><i class=\"fa fa-align-justify\"> 代理</i></a>";
                // append一个操作
                $actions->append($actionStr);

            });


        });
    }


}
