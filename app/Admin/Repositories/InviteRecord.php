<?php

namespace App\Admin\Repositories;

use App\Models\InviteRecord as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class InviteRecord extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
