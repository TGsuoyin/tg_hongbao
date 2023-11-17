<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Config;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Auth\Permission;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ConfigController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Config(['adminuser']), function (Grid $grid) {
            $grid->setActionClass(\App\Admin\Actions\TextActions::class);

            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('remark');
            $grid->column('value')->width('300px')->editable(true);
            $grid->column('group_id');
            $grid->column('adminuser.username','管理员');
//            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->equal('name','配置key')->width('250px');
                $filter->equal('group_id','群id')->width('250px');
            });
        });
    }
    public function edit($id, Content $content)
    {
        Permission::check('config_edit');
        return $content
            ->translation($this->translation())
            ->title($this->title())
            ->description($this->description()['edit'] ?? trans('admin.edit'))
            ->body($this->form()->edit($id));
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
        return Show::make($id, new Config(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('value');
            $show->field('group_id');
            $show->field('admin_id');
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
        return Form::make(new Config(), function (Form $form) {
            if($form->isCreating()){
                $form->text('group_id');
            }else{
                $form->text('group_id')->disable();
            }
            $form->display('id');
            $form->text('name');
            $form->text('value');
            $form->text('group_id');
//            $form->text('admin_id');
            $form->text('remark');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
