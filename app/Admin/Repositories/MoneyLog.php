<?php

namespace App\Admin\Repositories;

use App\Models\MoneyLog as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class MoneyLog extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
