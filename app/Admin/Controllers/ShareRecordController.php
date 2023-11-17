<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\ShareRecord;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ShareRecordController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ShareRecord(['user','shareuser','sender']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('lucky_id');
            $grid->column('amount');
            $grid->column('profit_amount');
            $grid->column('user.first_name','用户');
            $grid->column('group_id');
            $grid->column('remark');
            $grid->column('shareuser.first_name','分享者');
            $grid->column('sender.first_name','包主');
            $grid->column('created_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->equal('share_user_id');

            });
            // 禁用新增
            $grid->disableCreateButton();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
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
        return Show::make($id, new ShareRecord(), function (Show $show) {
            $show->field('id');
            $show->field('lucky_id');
            $show->field('amount');
            $show->field('tg_id');
            $show->field('group_id');
            $show->field('remark');
            $show->field('share_user_id');
            $show->field('sender_id');
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
        return Form::make(new ShareRecord(), function (Form $form) {
            $form->display('id');
            $form->text('lucky_id');
            $form->text('amount');
            $form->text('tg_id');
            $form->text('group_id');
            $form->text('remark');
            $form->text('share_user_id');
            $form->text('sender_id');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
