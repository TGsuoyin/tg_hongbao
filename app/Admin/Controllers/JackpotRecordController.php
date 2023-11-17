<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\JackpotRecord;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class JackpotRecordController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new JackpotRecord(['user','sender']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->model()->orderBy('id','desc');
            $grid->column('lucky_id');
            $grid->column('amount');
            $grid->column('user.first_name','中雷用户');
            $grid->column('group_id');
            $grid->column('remark');
            $grid->column('sender.first_name','发包用户');
            $grid->column('profit_amount');
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
        return Show::make($id, new JackpotRecord(), function (Show $show) {
            $show->field('id');
            $show->field('lucky_id');
            $show->field('amount');
            $show->field('tg_id');
            $show->field('group_id');
            $show->field('remark');
            $show->field('sender_id');
            $show->field('profit_amount');
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
        return Form::make(new JackpotRecord(), function (Form $form) {
            $form->display('id');
            $form->text('lucky_id');
            $form->text('amount');
            $form->text('tg_id');
            $form->text('group_id');
            $form->text('remark');
            $form->text('sender_id');
            $form->text('profit_amount');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
