<?php

namespace App\Models;

use App\Traits\DateTrait;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    use  DateTrait;
    protected $table = 'admin_users';
    protected $fillable = [

    ];
}



