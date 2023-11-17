<?php

namespace App\Admin\Actions\Grid;

use App\Admin\Forms\RechargeForm;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Grid\RowAction;


class RechargeAction extends RowAction
{
    use LazyWidget; // 使用异步加载功能


    protected $title = '<i class="fa fa-diamond"></i> 充值 ';


    public function render()
    {
        // 实例化表单类并传递自定义参数
        $form = RechargeForm::make()->payload(['id' => $this->getKey()]);

        return Modal::make()
            ->lg()
            ->title($this->title)
            ->body($form)
            ->button($this->title);
    }
}
