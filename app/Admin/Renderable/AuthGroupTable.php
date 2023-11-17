<?php

namespace App\Admin\Renderable;

use App\Admin\Repositories\AuthGroup;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use Dcat\Admin\Models\Administrator;

class AuthGroupTable extends LazyRenderable
{
    public function grid(): Grid
    {
        return Grid::make(new AuthGroup(), function (Grid $grid) {
            $grid->column('id', 'ID')->sortable();
            $grid->column('remark');
            $grid->column('group_id');

            $grid->quickSearch([ 'remark', 'group_id']);

            $grid->paginate(10);
            $grid->disableActions();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('remark')->width(4);
            });
        });
    }
}
