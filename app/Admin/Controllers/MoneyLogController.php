<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\MoneyLog;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class MoneyLogController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new MoneyLog(['user']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->model()->orderBy('id','desc');

            $grid->column('tg_id');
            $grid->column('user.username','用户名');
            $grid->column('user.first_name','昵称');
            $grid->column('group_id');
            $grid->column('balance','当时余额');
            $grid->column('amount')->setAttributes(['style' => 'font-size:16px;'])->if(function ($column) {

                // $column->getValue() 是当前字段的值
                // 返回 "真" 或 "假"，"真" 则执行 "if" 后面的代码
                return $column->getValue() > 0;
            })
                ->label('success')
                ->else()
                ->label('danger');
            $grid->column('type');
            $grid->column('remark');
            $grid->column('lucky_id');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->equal('tg_id','telegramID')->width('250px');
                $filter->equal('group_id','群ID')->width('250px');
                $filter->between('created_at', '创建时间')->datetime()->width('450px');

            });
            // 禁用新增
            $grid->disableCreateButton();
            $grid->actions(function (\App\Admin\Actions\TextActions $actions) {
                $actions->disableView();
                $actions->disableDelete();
                $actions->disableEdit();


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
        return Show::make($id, new MoneyLog(), function (Show $show) {
            $show->field('id');
            $show->field('amount');
            $show->field('tg_id');
            $show->field('group_id');
            $show->field('remark');
            $show->field('type');
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
        return Form::make(new MoneyLog(), function (Form $form) {
            $form->display('id');
            $form->text('amount');
            $form->text('tg_id');
            $form->text('group_id');
            $form->text('remark');
            $form->text('type');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
