<?php

namespace App\Models;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use  DateTrait;
    protected $table = 'config';
    protected $fillable = [
        'name',
        'value',
        'group_id',
        'user_id',
        'admin_id',
        'remark',
    ];
    public function adminuser()
    {
        return $this->hasOne(AdminUser::class,'id','admin_id');
    }
}
