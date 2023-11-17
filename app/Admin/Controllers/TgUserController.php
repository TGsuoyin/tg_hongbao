<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\RechargeAction;
use App\Admin\Actions\Grid\WithdrawAction;
use App\Admin\Metrics\TgUser\CommissionAmount;
use App\Admin\Metrics\TgUser\InviteAmount;
use App\Admin\Metrics\TgUser\JackpotAmount;
use App\Admin\Metrics\TgUser\LuckyAmount;
use App\Admin\Metrics\TgUser\LuckyHistory;
use App\Admin\Metrics\TgUser\LuckyHistoryAmount;
use App\Admin\Metrics\TgUser\LuckyHistoryThunder;
use App\Admin\Metrics\TgUser\LuckyHistoryThunderAmount;
use App\Admin\Metrics\TgUser\LuckyNum;
use App\Admin\Metrics\TgUser\LuckyNumThunder;
use App\Admin\Metrics\TgUser\LuckyThunderAmount;
use App\Admin\Metrics\TgUser\ProfitAmount;
use App\Admin\Metrics\TgUser\RechargeAmount;
use App\Admin\Metrics\TgUser\RewardAmount;
use App\Admin\Metrics\TgUser\ShareAmount;
use App\Admin\Metrics\TgUser\ToShareAmount;
use App\Admin\Metrics\TgUser\WithdrawAmount;
use App\Admin\Repositories\TgUser;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Http\Auth\Permission;

use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Widgets\Dropdown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TgUserController extends AdminController
{
    public function report(Content $content,Request $request){
        $tgId = $request->get('tg_id');
        $username = $request->get('username')??'';
        $option = $request->get('option')??'';
        $options = [
            '1' => '今日',
            '7' => '最近7天',
            '15' => '最近15天',
            '30' => '最近一个月',
            '180' => '最近半年',
            '365' => '最近一年',
        ];
        $btn = $options[$option]??$options[1];
        Admin::style(
            <<<CSS
    .mb10 {
        margin-bottom: 10px;
    }
CSS
        );
        $dropdown = Dropdown::make($options)
            ->button($btn) // 设置按钮
            ->buttonClass('btn btn-white  waves-effect mb10') // 设置按钮样式
            ->map(function ($label, $key) use($tgId){
                // 格式化菜单选项
                $url = admin_url('tgusers/report?tg_id='.$tgId.'&option='.$key);

                return "<a href='$url'>{$label}</a>";
            });
        return $content
            ->header('个人报表【'.$username.'('.$tgId.')】')
            ->description('')
            ->body($dropdown)
            ->body(function ($row) use($tgId,$option) {
                $row->column(12, function (Column $column) use($tgId,$option) {
                    $column->row(function (Row $row) use($tgId,$option) {
                        $row->column(3, new LuckyNum(['tg_id' => $tgId,'option'=>$option]));
                        $row->column(3, new LuckyNumThunder(['tg_id' => $tgId,'option'=>$option]));
                        $row->column(3, new LuckyHistory(['tg_id' => $tgId,'option'=>$option]));
                        $row->column(3, new LuckyHistoryThunder(['tg_id' => $tgId,'option'=>$option]));
                    });

                });
                $row->column(12, function (Column $column) use($tgId,$option) {
                    $column->row(function (Row $row)use($tgId,$option) {
                        $row->column(3, new LuckyAmount(['tg_id' => $tgId,'option'=>$option]));
                        $row->column(3, new LuckyThunderAmount(['tg_id' => $tgId,'option'=>$option]));
                        $row->column(3, new LuckyHistoryAmount(['tg_id' => $tgId,'option'=>$option]));
                        $row->column(3, new LuckyHistoryThunderAmount(['tg_id' => $tgId,'option'=>$option]));
                    });
                });
                $row->column(12, function (Column $column) use($tgId,$option) {
                    $column->row(function (Row $row)use($tgId,$option) {
                        $row->column(3, new InviteAmount(['tg_id' => $tgId,'option'=>$option]));
                        $row->column(3, new RewardAmount(['tg_id' => $tgId,'option'=>$option]));
                        $row->column(3, new ShareAmount(['tg_id' => $tgId,'option'=>$option]));
                        $row->column(3, new RechargeAmount(['tg_id' => $tgId,'option'=>$option]));
                    });

                });
                $row->column(12, function (Column $column) use($tgId,$option) {
                    $column->row(function (Row $row)use($tgId,$option) {
                        $row->column(3, new WithdrawAmount(['tg_id' => $tgId,'option'=>$option]));
                        $row->column(3, new CommissionAmount(['tg_id' => $tgId,'option'=>$option]));
                        $row->column(3, new ToShareAmount(['tg_id' => $tgId,'option'=>$option]));
                        $row->column(3, new JackpotAmount(['tg_id' => $tgId,'option'=>$option]));

                    });

                });
                $row->column(12, function (Column $column) use($tgId,$option) {
                    $column->row(function (Row $row)use($tgId,$option) {

                        $row->column(3, new ProfitAmount(['tg_id' => $tgId,'option'=>$option]));

                    });

                });
            });
    }
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new TgUser(), function (Grid $grid) {

            $grid->setActionClass(\App\Admin\Actions\TextActions::class);
            $grid->model()->orderBy('id','desc');
            $grid->column('id')->sortable();
            $grid->column('username');
            $grid->column('first_name');
            $grid->column('tg_id');
            $grid->column('group_id');
            $grid->column('balance')->sortable();
            $grid->column('status')->using([0 => '禁用', 1 => '正常']);
            $grid->column('online','是否在线')->filter()->display(function () {
                $res ='否';
                if($this->has_thunder|| $this->pass_mine || $this->auto_get || $this->no_thunder || $this->get_mine ){
                    if($this->online !=1){
                        \App\Models\TgUser::query()->where('id',$this->id)->update(['online'=>1]);
                    }
                    $res = '在线';
                }else{
                    if($this->online ==1){
                        \App\Models\TgUser::query()->where('id',$this->id)->update(['online'=>0]);
                    }
                }
                return $res;
            });

            $grid->column('invite_user');
            $grid->column('send_chance');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->equal('username','用户名')->width('250px');
                $filter->equal('first_name','用户昵称')->width('250px');
                $filter->equal('tg_id','用户id')->width('250px');
                $filter->equal('group_id','群id')->width('250px');
                $filter->equal('online','是否在线')->width('250px');

            });
            // 禁用新增
            $grid->disableCreateButton();
            $grid->actions(function (\App\Admin\Actions\TextActions $actions) {
                $actions->disableDelete();
                $actions->append(new RechargeAction());
                $actions->append(new WithdrawAction());
                $id = $actions->row->tg_id;
                $userName = $actions->row->first_name!=''?$actions->row->first_name:$actions->row->usernmae;
                $actionStr = "<a href=\"/admin/luckhistory?user_id={$id}\"><i class=\"fa fa-align-justify\"> 抢包记录</i></a>";
                $actionStr .= " <a href=\"/admin/inviterecord?invite_user_id={$id}\"><i class=\"fa fa-binoculars\"> 邀请奖励记录</i></a>";
                $actionStr .= " <a href=\"/admin/rewardrecord?tg_id={$id}\"><i class=\"fa fa-diamond\"> 中奖记录</i></a>";
                $actionStr .= " <a href=\"/admin/sharerecord?share_user_id={$id}\"><i class=\"fa fa-optin-monster\"> 代理抽成记录</i></a>";
                $actionStr .= " <a href=\"/admin/tgusers/report?tg_id={$id}&username={$userName}\"  class=\"text-info\"><i class=\"fa fa-envelope-o\"> 个人报表</i></a>";
                $actionStr .= " <a href=\"/admin/moneylog?tg_id={$id}\"  class=\"text-success\"><i class=\"fa fa-file-text-o\"> 资金明细</i></a>";
                // append一个操作
                $actions->append($actionStr);

            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new TgUser(), function (Show $show) {
            $show->field('id');
            $show->field('username');
            $show->field('first_name');
            $show->field('tg_id');
            $show->field('balance');
            $show->field('status')->using(['0' => '禁用', '1' => '正常']);
            $show->field('send_chance');
            $show->field('invite_user');
            $show->field('created_at');
            $show->field('updated_at');
            $show->panel()
                ->tools(function ($tools) {
                    $tools->disableEdit();
                    $tools->disableDelete();
                    // 显示快捷编辑按钮
                });
        });

    }
    public function edit($id, Content $content)
    {
        Permission::check('tguser_edit');
        return $content
            ->translation($this->translation())
            ->title($this->title())
            ->description($this->description()['edit'] ?? trans('admin.edit'))
            ->body($this->form()->edit($id));
    }
    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new TgUser(), function (Form $form) {
            $form->display('id');
            $form->text('username')->disable();
            $form->text('first_name');
            $form->text('tg_id')->disable();
            $form->text('balance')->disable();
            $form->radio('status')->options(['0' => '禁用', '1' => '正常'])->default('1');
            $form->text('invite_user');
            $form->number('send_chance');
            $form->radio('has_thunder')->options(['0' => '否', '1'=> '是'])->default('0');
            $form->radio('no_thunder')->options(['0' => '否', '1'=> '是'])->default('0');
            $form->radio('pass_mine')->options(['0' => '否', '1'=> '是'])->default('0');
            $form->radio('get_mine')->options(['0' => '否', '1'=> '是'])->default('0');
            $form->radio('auto_get')->options(['0' => '否', '1'=> '是'])->default('0');

            $form->tools(function (Form\Tools $tools) {
                $tools->disableDelete();
            });
        });
    }
}
