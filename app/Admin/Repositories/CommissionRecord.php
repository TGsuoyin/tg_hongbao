<?php

namespace App\Admin\Repositories;

use App\Models\CommissionRecord as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class CommissionRecord extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
