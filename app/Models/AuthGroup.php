<?php

namespace App\Models;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Model;

class AuthGroup extends Model
{
    use  DateTrait;
    protected $table = 'auth_group';
    protected $fillable = [
        'group_id',
        'remark',
        'status',
        'service_url',
        'recharge_url',
        'channel_url',
        'photo_id',
        'admin_id',
    ];
    public function adminuser()
    {
        return $this->hasOne(AdminUser::class,'id','admin_id');
    }

}
