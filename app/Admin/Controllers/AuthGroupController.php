<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\AuthGroup;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Http\Auth\Permission;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class AuthGroupController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new AuthGroup(['adminuser']), function (Grid $grid) {
            $grid->setActionClass(\App\Admin\Actions\TextActions::class);
            $grid->model()->orderBy('id','desc');
            $grid->column('id')->sortable();
            $grid->column('group_id');
            $grid->column('remark');
            $grid->column('status')->switch('', true);
            $grid->column('service_url')->width('12%')->setAttributes(['style' => 'word-wrap:break-word;word-break:break-all; ']);
            $grid->column('recharge_url')->width('12%')->setAttributes(['style' => 'word-wrap:break-word;word-break:break-all; ']);
            $grid->column('channel_url')->width('12%')->setAttributes(['style' => 'word-wrap:break-word;word-break:break-all; ']);
            $grid->column('photo_id')->width('15%')->setAttributes(['style' => 'word-wrap:break-word;word-break:break-all; ']);
            $grid->column('adminuser.username','管理员')->width('5%');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->like('remark','备注')->width('250px');
                $filter->equal('group_id','群id')->width('250px');
            });
            $grid->actions(function (\App\Admin\Actions\TextActions $actions) {
                $group_id = $actions->row->group_id;
                $actionStr = "<a href=\"/admin/configs?group_id={$group_id}\" style='margin-right: 6px;'><i class=\"fa fa-sliders\"> 配置</i></a>";
                // append一个操作
                $actionStr .= "<a href=\"/admin/tgusers?group_id={$group_id}\" style='margin-right: 6px;'><i class=\"fa fa-users\"> 查看用户</i></a>";
                $actionStr .= "<a href=\"/admin/rechargerecord?group_id={$group_id}\" style='margin-right: 6px;'><i class=\"fa fa-diamond\"> 充值记录</i></a>";
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
        return Show::make($id, new AuthGroup(), function (Show $show) {
            $show->field('id');
            $show->field('group_id');
            $show->field('remark');
            $show->field('status')->using(['0' => '禁用', '1' => '正常']);
            $show->field('service_url');
            $show->field('recharge_url');
            $show->field('channel_url');
            $show->field('photo_id');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }
    public function edit($id, Content $content)
    {
        Permission::check('group_edit');
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
        return Form::make(new AuthGroup(), function (Form $form) {
            $form->display('id');
            $form->text('group_id')->required()->rules(function (Form $form) {

                // 如果不是编辑状态，则添加字段唯一验证
                if (!$id = $form->model()->id) {
                    return 'unique:auth_group,group_id';
                }

            });;
            $form->text('remark');
            $form->radio('status')->options(['0' => '禁用', '1'=> '正常'])->default('1');
            $form->text('service_url')->required();
            $form->text('recharge_url')->required();
            $form->text('channel_url')->required();
            $form->text('photo_id')->required();

//            $form->display('created_at');
//            $form->display('updated_at');
        });
    }

}
