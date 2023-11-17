<?php

namespace App\Admin\Repositories;

use App\Models\AuthGroup as Model;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Repositories\EloquentRepository;

class AuthGroup extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;

    public function store(Form $form)
    {
        // 获取待新增的数据
        $attributes = $form->updates();

        $model = $this->model();
        // 执行你的新增逻辑
        foreach ($attributes as $column => $value) {
            $model->setAttribute($column, $value);
        }
        $model->admin_id = Admin::user()->id;
        $result = $model->save();

        if ($result) {
            $tgbotConfig = config('tgbot');
            foreach ($tgbotConfig as $key => $val) {
                if (\App\Models\Config::query()->where('name', $key)->where('group_id', $attributes['group_id'])->count() == 0) {
                    $insert = [
                        'name' => $key,
                        'value' => $val,
                        'group_id' => $attributes['group_id'],
                        'admin_id' => Admin::user()->id,
                        'remark' => trans('admin.tgbot.' . $key),
                    ];
                    \App\Models\Config::query()->create($insert);
                }
            }


            // 返回新增记录id或bool值
            return true;
        } else {
            return false;
        }
    }

    public function delete(Form $form, array $originalData)
    {
        $groupIds = array_column($originalData,'group_id');
        // 当批量删除时id为多个
        $id = explode(',', $form->getKey());

        // $deletingData 是 getDataWhenDeleting 接口返回的数据

        // 执行你的逻辑
        $model = $this->model();
        $res = $model->whereIn('id',$id)->delete();
        if($res){
            if (\App\Models\Config::query()->whereIn('group_id', $groupIds)->count() > 0) {
                \App\Models\Config::query()->whereIn('group_id', $groupIds)->delete();
            }
        }

        return $res;
    }
}
