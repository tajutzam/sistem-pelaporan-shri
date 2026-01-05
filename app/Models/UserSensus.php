<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSensus extends Model
{
    //
    protected $guarded = ['id'];

    protected $table = 'users_sensus_logs';
}
