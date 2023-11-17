<?php

namespace App\Models;

use App\Traits\DateTrait;

use Illuminate\Database\Eloquent\Model;

class InviteRecord extends Model
{

    use  DateTrait;
    protected $table = 'invite_record';
    protected $fillable = [
        'amount',
        'tg_id',
        'group_id',
        'remark',
        'invite_user_id',
    ];
    public function user()
    {
        return $this->hasOne(TgUser::class,'tg_id','tg_id');
    }
    public function inviteuser()
    {
        return $this->hasOne(TgUser::class,'tg_id','invite_user_id');
    }
}
