<?php

namespace App\Admin\Repositories;

use App\Models\JackpotRecord as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class JackpotRecord extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
