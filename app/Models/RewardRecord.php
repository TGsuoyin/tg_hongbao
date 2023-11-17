<?php

namespace App\Models;

use App\Traits\DateTrait;

use Illuminate\Database\Eloquent\Model;

class RewardRecord extends Model
{
    use  DateTrait;
    protected $table = 'reward_record';
    protected $fillable = [
        'lucky_id',
        'amount',
        'tg_id',
        'group_id',
        'remark',
        'sender_id',
        'reward_num',
        'type',
    ];
    public function user()
    {
        return $this->hasOne(TgUser::class,'tg_id','tg_id');
    }
    public function sender()
    {
        return $this->hasOne(TgUser::class,'tg_id','sender_id');
    }
}



