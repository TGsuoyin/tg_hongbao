<?php

namespace App\Admin\Repositories;

use App\Models\JackpotReward as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class JackpotReward extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
