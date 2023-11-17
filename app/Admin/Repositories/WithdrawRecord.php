<?php

namespace App\Admin\Repositories;

use App\Models\WithdrawRecord as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class WithdrawRecord extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
