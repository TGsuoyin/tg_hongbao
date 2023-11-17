<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\WithdrawRecord;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class WithdrawRecordController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new WithdrawRecord(['adminuser']), function (Grid $grid) {
            $grid->model()->orderBy('id','desc');
            $grid->column('id')->sortable();
            $grid->column('tg_id');
            $grid->column('username');
            $grid->column('first_name');
            $grid->column('amount');
            $grid->column('status')->using([0 => '未转账', 1 => '已转账'])->label([
                'default' => 'danger', // 设置默认颜色，不设置则默认为 default
                0 => 'danger',
                1 => 'success',
            ])->filter();
            $grid->column('address');
            $grid->column('addr_type');
            $grid->column('group_id');
            $grid->column('remark');
            $grid->column('adminuser.username','管理员');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->equal('group_id','群id')->width('250px');

            });
            // 禁用新增
            $grid->disableCreateButton();
            $grid->actions(function (\App\Admin\Actions\TextActions $actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->disableView();


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
        return Show::make($id, new WithdrawRecord(), function (Show $show) {
            $show->field('id');
            $show->field('tg_id');
            $show->field('username');
            $show->field('first_name');
            $show->field('amount');
            $show->field('status');
            $show->field('address');
            $show->field('addr_type');
            $show->field('group_id');
            $show->field('remark');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new WithdrawRecord(), function (Form $form) {
            $form->display('id');
            $form->text('tg_id');
            $form->text('username');
            $form->text('first_name');
            $form->text('amount');
            $form->text('status');
            $form->text('address');
            $form->text('addr_type');
            $form->text('group_id');
            $form->text('remark');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
