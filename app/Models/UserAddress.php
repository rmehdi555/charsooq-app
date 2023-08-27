<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use SoftDeletes;

    protected $table = 'users_addresses';

    protected $fillable = [
        'postalcode',
        'content',
        'user_id',
        'state_id',
        'city_id',
    ];
}
