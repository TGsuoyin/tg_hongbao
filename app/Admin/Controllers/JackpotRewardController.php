<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\JackpotReward;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class JackpotRewardController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new JackpotReward(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('lucky_id');
            $grid->column('amount');
            $grid->column('tg_id');
            $grid->column('group_id');
            $grid->column('remark');
            $grid->column('sender_id');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
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
        return Show::make($id, new JackpotReward(), function (Show $show) {
            $show->field('id');
            $show->field('lucky_id');
            $show->field('amount');
            $show->field('tg_id');
            $show->field('group_id');
            $show->field('remark');
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
        return Form::make(new JackpotReward(), function (Form $form) {
            $form->display('id');
            $form->text('lucky_id');
            $form->text('amount');
            $form->text('tg_id');
            $form->text('group_id');
            $form->text('remark');
            $form->text('sender_id');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
