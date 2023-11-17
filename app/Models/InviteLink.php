<?php

namespace App\Models;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Model;

class InviteLink extends Model
{
    use  DateTrait;
    protected $table = 'invite_link';
    protected $fillable = [
        'tg_id',
        'invite_link',
        'group_id',

    ];


}
