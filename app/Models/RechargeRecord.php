<?php

namespace App\Models;

use App\Traits\DateTrait;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RechargeRecord extends Model
{
    use  DateTrait;
    use SoftDeletes;
    protected $table = 'recharge_record';
    protected $fillable = [
        'amount',
        'tg_id',
        'group_id',
        'remark',
        'admin_id',
        'first_name',
        'username',
        'status',
        'type',
    ];
    public function adminuser()
    {
        return $this->hasOne(AdminUser::class,'id','admin_id');
    }

}



