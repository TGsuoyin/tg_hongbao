<?php

namespace App\Models;

use App\Traits\DateTrait;

use Illuminate\Database\Eloquent\Model;

class JackpotReward extends Model
{
    use  DateTrait;
    protected $table = 'jackpot_reward';
    protected $fillable = [
        'lucky_id',
        'amount',
        'tg_id',
        'group_id',
        'remark',
        'sender_id',
    ];

}



