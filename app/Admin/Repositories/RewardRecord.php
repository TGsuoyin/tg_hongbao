<?php

namespace App\Admin\Repositories;

use App\Models\RewardRecord as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class RewardRecord extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
