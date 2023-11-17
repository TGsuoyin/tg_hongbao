<?php

namespace App\Models;

use App\Traits\DateTrait;

use Illuminate\Database\Eloquent\Model;

class JackpotPool extends Model
{
    use  DateTrait;
    protected $table = 'jackpot_pool';
    protected $fillable = [
        'group_id',
        'balance',
    ];

}



