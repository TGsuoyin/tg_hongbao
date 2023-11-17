<?php

namespace App\Admin\Repositories;

use App\Models\ShareRecord as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class ShareRecord extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;

}
